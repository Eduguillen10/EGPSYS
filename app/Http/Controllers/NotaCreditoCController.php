<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotaCreditoCRequest;
use App\Models\NotaCreditoC;
use App\Models\NotaCreditocDetalle;
use App\Services\LibroComprasService;
use App\Services\MovimientoStockService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class NotaCreditoCController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = trim((string) $request->get('searchText'));
        $queryProveedor = trim((string) $request->get('searchText2'));
        $queryFecha = trim((string) $request->get('searchText3'));
        $querySucursal = trim((string) $request->get('searchText4'));
        $queryComprobante = trim((string) $request->get('searchText5'));
        $queryRuc = trim((string) $request->get('searchText6'));

        $builder = DB::table('nota_credito_compra as nc')
            ->join('compras as c', 'nc.idcompra', '=', 'c.idcompra')
            ->join('sucursales as s', 'nc.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'nc.iddeposito', '=', 'dep.iddeposito')
            ->join('proveedores as p', 'nc.idproveedor', '=', 'p.idproveedor')
            ->join('users as u', 'nc.idusuario', '=', 'u.id')
            ->select(
                'nc.idnota_creditoc',
                'nc.idcompra',
                'u.name as usuario',
                's.descripcion as sucursal',
                'dep.descripcion as deposito',
                'p.razonsocial as proveedor',
                'p.ruc as num_documento',
                'nc.fecha_registro',
                'nc.montoiva10',
                'nc.montoiva5',
                'nc.montogravada10',
                'nc.montogravada5',
                'nc.montoexenta',
                'nc.montonota_credito_compra',
                'nc.timbrado',
                'nc.concepto',
                'nc.nro_factura',
                'nc.estado',
                'nc.fecha_factura',
                'nc.fecha_vencimiento',
                'c.nro_factura as nro_factura_compra'
            );

        if ($query !== '') {
            $builder->where('nc.idnota_creditoc', 'LIKE', '%' . $query . '%');
        }

        if ($queryProveedor !== '') {
            $builder->where('p.razonsocial', 'LIKE', '%' . $queryProveedor . '%');
        }

        if ($queryFecha !== '') {
            $builder->where('nc.fecha_registro', 'LIKE', '%' . $queryFecha . '%');
        }

        if ($querySucursal !== '') {
            $builder->where('s.descripcion', 'LIKE', '%' . $querySucursal . '%');
        }

        if ($queryComprobante !== '') {
            $builder->where('nc.nro_factura', 'LIKE', '%' . $queryComprobante . '%');
        }

        if ($queryRuc !== '') {
            $builder->where('p.ruc', 'LIKE', '%' . $queryRuc . '%');
        }

        $nota_creditoc = $builder->orderByDesc('nc.idnota_creditoc')->paginate(7);

        return view('compras.nota_creditoc.index', [
            'nota_creditoc' => $nota_creditoc,
            'searchText' => $query,
            'searchText2' => $queryProveedor,
            'searchText3' => $queryFecha,
            'searchText4' => $querySucursal,
            'searchText5' => $queryComprobante,
            'searchText6' => $queryRuc,
        ]);
    }

    public function create(Request $request)
    {
        $suc = (int) Auth::user()->trabaja_sucursal;
        $idcompra = $request->integer('idcompra') ?: null;
        $compra = $idcompra ? $this->datosCompraParaNota($idcompra) : null;

        if ($idcompra && ! $compra) {
            return Redirect::route('nota_creditoc.create')
                ->with('error', 'La compra seleccionada no existe o no esta disponible para nota de credito.');
        }

        $compras = $this->comprasDisponibles($suc);
        $sucursal = DB::table('sucursales')->where('idsucursal', $suc)->first();
        $detalles = $compra ? $this->detallesCompraDisponibles((int) $compra->idcompra) : collect();

        return view('compras.nota_creditoc.create', [
            'compras' => $compras,
            'compra' => $compra,
            'detalles' => $detalles,
            'sucursal' => $sucursal,
        ]);
    }

    public function store(NotaCreditoCRequest $request)
    {
        try {
            DB::beginTransaction();

            $compra = $this->bloquearCompra((int) $request->input('idcompra'));

            if (! $compra) {
                throw new Exception('La compra seleccionada no existe.');
            }

            if ($this->estadoEsCancelado($compra->estado)) {
                throw new Exception('La compra seleccionada se encuentra cancelada.');
            }

            if ((int) $compra->idsucursal !== (int) Auth::user()->trabaja_sucursal) {
                throw new Exception('La compra seleccionada no pertenece a la sucursal actual.');
            }

            $cuenta = DB::table('cuentas_a_pagar')
                ->where('idcompra', (int) $compra->idcompra)
                ->lockForUpdate()
                ->first();

            if (! $cuenta) {
                throw new Exception('La compra seleccionada no posee cuenta a pagar asociada.');
            }

            $this->validarComprobanteDuplicado(
                (int) $compra->idproveedor,
                (string) $request->input('timbrado'),
                (string) $request->input('nro_factura')
            );

            $idproductos = $request->input('idproducto', []);
            $cantidades = $request->input('cantidad', []);
            $precios = $request->input('precio_compra', []);

            $sumiva10 = 0;
            $sumiva5 = 0;
            $sumgravada10 = 0;
            $sumgravada5 = 0;
            $sumexenta = 0;
            $summontoitems = 0;
            $lineas = [];

            foreach ($idproductos as $index => $idproducto) {
                $idproducto = (int) $idproducto;
                $cantidad = (float) ($cantidades[$index] ?? 0);
                $precioCompra = (float) ($precios[$index] ?? 0);

                $this->validarDetalleDevolucion(
                    (int) $compra->idcompra,
                    (int) $compra->idsucursal,
                    (int) $compra->iddeposito,
                    $idproducto,
                    $cantidad
                );

                $linea = $this->calcularLinea($idproducto, $cantidad, $precioCompra, count($lineas) + 1);
                $lineas[] = $linea;

                $sumiva10 += $linea['iva10'];
                $sumiva5 += $linea['iva5'];
                $sumgravada10 += $linea['gravada10'];
                $sumgravada5 += $linea['gravada5'];
                $sumexenta += $linea['exenta'];
                $summontoitems += $linea['montoitems'];
            }

            if ($summontoitems <= 0) {
                throw new Exception('Debe agregar al menos un producto valido al detalle.');
            }

            if ($summontoitems > (int) $cuenta->montoapagar) {
                throw new Exception('El monto de la nota de credito no puede superar el saldo pendiente de la cuenta a pagar.');
            }

            $notaCredito = NotaCreditoC::create([
                'idcompra' => (int) $compra->idcompra,
                'idsucursal' => (int) $compra->idsucursal,
                'iddeposito' => (int) $compra->iddeposito,
                'idproveedor' => (int) $compra->idproveedor,
                'ruc' => (string) $compra->ruc,
                'nro_factura' => $request->input('nro_factura'),
                'timbrado' => $request->input('timbrado'),
                'fecha_registro' => now()->toDateString(),
                'fecha_factura' => $request->input('fecha_factura'),
                'fecha_vencimiento' => $compra->fecha_vencimiento,
                'concepto' => $request->input('concepto'),
                'montoiva10' => $sumiva10,
                'montoiva5' => $sumiva5,
                'montogravada10' => $sumgravada10,
                'montogravada5' => $sumgravada5,
                'montoexenta' => $sumexenta,
                'montonota_credito_compra' => $summontoitems,
                'estado' => 'Realizado',
                'idusuario' => Auth::id(),
            ]);

            foreach ($lineas as $linea) {
                NotaCreditocDetalle::create(array_merge($linea, [
                    'idnota_creditoc' => (int) $notaCredito->idnota_creditoc,
                ]));

                $this->registrarSalidaStockNotaCredito(
                    (int) $compra->idsucursal,
                    (int) $compra->iddeposito,
                    (int) $linea['idproducto'],
                    (float) $linea['cantidad'],
                    (int) $notaCredito->idnota_creditoc
                );
            }

            $this->ajustarCuentaPagar((int) $compra->idcompra, $summontoitems);
            $this->actualizarLibroCompras((int) $compra->idcompra);

            DB::commit();

            return Redirect::route('nota_creditoc.show', $notaCredito->idnota_creditoc)
                ->with('success', 'Operacion exitosa.');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $datos = $this->datosNotaCredito((int) $id);

        if (! $datos['nota_creditoc']) {
            abort(404);
        }

        return view('compras.nota_creditoc.show', $datos);
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $notaCredito = NotaCreditoC::where('idnota_creditoc', (int) $id)->lockForUpdate()->firstOrFail();

            if ($this->estadoEsCancelado($notaCredito->estado)) {
                DB::rollBack();

                return Redirect::route('nota_creditoc.index')->with('info', 'La nota de credito ya estaba cancelada.');
            }

            $detalles = NotaCreditocDetalle::where('idnota_creditoc', (int) $notaCredito->idnota_creditoc)
                ->lockForUpdate()
                ->get();

            foreach ($detalles as $detalle) {
                $this->registrarEntradaStockNotaCredito(
                    (int) $notaCredito->idsucursal,
                    (int) $notaCredito->iddeposito,
                    (int) $detalle->idproducto,
                    (float) $detalle->cantidad,
                    (int) $notaCredito->idnota_creditoc
                );
            }

            DB::table('cuentas_a_pagar')
                ->where('idcompra', (int) $notaCredito->idcompra)
                ->increment('montoapagar', (int) $notaCredito->montonota_credito_compra);

            $this->normalizarEstadoCuentaPagar((int) $notaCredito->idcompra);

            $notaCredito->estado = 'Cancelado';
            $notaCredito->save();

            $this->actualizarLibroCompras((int) $notaCredito->idcompra);

            DB::commit();

            return Redirect::route('nota_creditoc.index')
                ->with('success', 'Nota de credito anulada correctamente.');
        } catch (Exception $e) {
            DB::rollBack();

            return Redirect::route('nota_creditoc.index')
                ->with('error', 'Error al anular la nota de credito: ' . $e->getMessage());
        }
    }

    private function comprasDisponibles(int $idsucursal)
    {
        return DB::table('compras as c')
            ->join('proveedores as p', 'c.idproveedor', '=', 'p.idproveedor')
            ->join('depositos as d', 'c.iddeposito', '=', 'd.iddeposito')
            ->join('cuentas_a_pagar as cp', 'c.idcompra', '=', 'cp.idcompra')
            ->select(
                'c.idcompra',
                'c.nro_factura',
                'c.fecha_factura',
                'c.timbrado',
                'p.razonsocial as proveedor',
                'p.ruc',
                'd.descripcion as deposito',
                'cp.montoapagar',
                'cp.estado as estado_cuenta'
            )
            ->where('c.idsucursal', '=', $idsucursal)
            ->whereIn('c.estado', ['Realizado', 'R'])
            ->where('cp.montoapagar', '>', 0)
            ->orderByDesc('c.idcompra')
            ->get();
    }

    private function datosCompraParaNota(int $idcompra)
    {
        return DB::table('compras as c')
            ->join('proveedores as p', 'c.idproveedor', '=', 'p.idproveedor')
            ->join('depositos as d', 'c.iddeposito', '=', 'd.iddeposito')
            ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
            ->join('users as u', 'c.idusuario', '=', 'u.id')
            ->join('cuentas_a_pagar as cp', 'c.idcompra', '=', 'cp.idcompra')
            ->select(
                'c.idcompra',
                'c.idsucursal',
                'c.iddeposito',
                'c.idproveedor',
                'c.idusuario',
                'c.nro_factura',
                'c.timbrado',
                'c.fecha_factura',
                'c.fecha_vencimiento',
                'c.ruc',
                'c.estado',
                'c.montocompra',
                'p.razonsocial as proveedor',
                'd.descripcion as deposito',
                's.descripcion as sucursal',
                'u.name as usuario',
                'cp.montoapagar',
                'cp.montopagado',
                'cp.estado as estado_cuenta'
            )
            ->where('c.idcompra', '=', $idcompra)
            ->whereIn('c.estado', ['Realizado', 'R'])
            ->first();
    }

    private function bloquearCompra(int $idcompra)
    {
        return DB::table('compras')
            ->where('idcompra', '=', $idcompra)
            ->lockForUpdate()
            ->first();
    }

    private function detallesCompraDisponibles(int $idcompra)
    {
        return DB::table('compra_detalle as d')
            ->join('productos as p', 'd.idproducto', '=', 'p.idproducto')
            ->select(
                'd.idcompra_detalle',
                'd.idproducto',
                'p.descripcion as producto',
                'd.items',
                'd.cantidad',
                'd.precio_compra',
                'd.montoitems'
            )
            ->where('d.idcompra', '=', $idcompra)
            ->orderBy('d.items')
            ->get()
            ->map(function ($detalle) use ($idcompra) {
                $devuelto = DB::table('nota_credito_compra_detalle as ncd')
                    ->join('nota_credito_compra as nc', 'ncd.idnota_creditoc', '=', 'nc.idnota_creditoc')
                    ->where('nc.idcompra', '=', $idcompra)
                    ->whereNotIn('nc.estado', ['Cancelado', 'Anulado', 'Anulada', 'A'])
                    ->where('ncd.idproducto', '=', $detalle->idproducto)
                    ->sum('ncd.cantidad');

                $detalle->cantidad_disponible = max(0, (float) $detalle->cantidad - (float) $devuelto);

                return $detalle;
            })
            ->filter(fn ($detalle) => $detalle->cantidad_disponible > 0)
            ->values();
    }

    private function datosNotaCredito(int $id): array
    {
        $nota_creditoc = DB::table('nota_credito_compra as nc')
            ->join('compras as c', 'nc.idcompra', '=', 'c.idcompra')
            ->join('sucursales as s', 'nc.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'nc.iddeposito', '=', 'dep.iddeposito')
            ->join('proveedores as p', 'nc.idproveedor', '=', 'p.idproveedor')
            ->join('users as u', 'nc.idusuario', '=', 'u.id')
            ->select(
                'nc.idnota_creditoc',
                'nc.idcompra',
                'u.name as usuario',
                's.descripcion as sucursal',
                'dep.descripcion as deposito',
                'p.razonsocial as proveedor',
                'p.ruc as num_documento',
                'nc.fecha_registro',
                'nc.montoiva10',
                'nc.montoiva5',
                'nc.montogravada10',
                'nc.montogravada5',
                'nc.montoexenta',
                'nc.montonota_credito_compra',
                'nc.timbrado',
                'nc.concepto',
                'nc.nro_factura',
                'nc.estado',
                'nc.fecha_factura',
                'nc.fecha_vencimiento',
                'c.nro_factura as nro_factura_compra'
            )
            ->where('nc.idnota_creditoc', '=', $id)
            ->first();

        $detalles = DB::table('nota_credito_compra_detalle as d')
            ->join('productos as p', 'd.idproducto', '=', 'p.idproducto')
            ->select(
                'p.descripcion as producto',
                'd.cantidad',
                'd.precio_compra',
                'd.iva10',
                'd.iva5',
                'd.gravada10',
                'd.gravada5',
                'd.exenta',
                'd.montoitems'
            )
            ->where('d.idnota_creditoc', '=', $id)
            ->orderBy('d.items')
            ->get();

        return compact('nota_creditoc', 'detalles');
    }

    private function validarComprobanteDuplicado(int $idproveedor, string $timbrado, string $nroFactura): void
    {
        $timbrado = trim($timbrado);
        $nroFactura = trim($nroFactura);

        $existe = DB::table('nota_credito_compra')
            ->where('idproveedor', '=', $idproveedor)
            ->where('nro_factura', '=', $nroFactura)
            ->where('timbrado', '=', $timbrado)
            ->whereNotIn('estado', ['Cancelado', 'Anulado', 'Anulada', 'A'])
            ->exists();

        if ($existe) {
            throw new Exception('Ya existe una nota de credito activa con el mismo proveedor, timbrado y numero de comprobante.');
        }
    }

    private function validarDetalleDevolucion(
        int $idcompra,
        int $idsucursal,
        int $iddeposito,
        int $idproducto,
        float $cantidad
    ): void {
        if ($cantidad <= 0) {
            throw new Exception('La cantidad devuelta debe ser mayor a cero.');
        }

        $detalleCompra = DB::table('compra_detalle')
            ->where('idcompra', '=', $idcompra)
            ->where('idproducto', '=', $idproducto)
            ->first();

        if (! $detalleCompra) {
            throw new Exception('El producto seleccionado no pertenece a la compra original.');
        }

        $devuelto = DB::table('nota_credito_compra_detalle as ncd')
            ->join('nota_credito_compra as nc', 'ncd.idnota_creditoc', '=', 'nc.idnota_creditoc')
            ->where('nc.idcompra', '=', $idcompra)
            ->whereNotIn('nc.estado', ['Cancelado', 'Anulado', 'Anulada', 'A'])
            ->where('ncd.idproducto', '=', $idproducto)
            ->sum('ncd.cantidad');

        $disponibleParaDevolver = (float) $detalleCompra->cantidad - (float) $devuelto;

        if ($cantidad > $disponibleParaDevolver) {
            throw new Exception('La cantidad devuelta supera la cantidad disponible para devolver del producto.');
        }

        $stock = DB::table('stock')
            ->where('idsucursal', '=', $idsucursal)
            ->where('iddeposito', '=', $iddeposito)
            ->where('idproducto', '=', $idproducto)
            ->lockForUpdate()
            ->first();

        if (! $stock || (float) $stock->cantidad < $cantidad) {
            $producto = DB::table('productos')->where('idproducto', '=', $idproducto)->value('descripcion') ?? $idproducto;
            $disponibleStock = $stock ? (float) $stock->cantidad : 0;

            throw new Exception("No existe stock suficiente para devolver {$producto}. Disponible: {$disponibleStock}, requerido: {$cantidad}.");
        }
    }

    private function calcularLinea(int $idproducto, float $cantidad, float $precioCompra, int $items): array
    {
        $producto = DB::table('productos as prod')
            ->join('tipo_impuesto as ti', 'prod.idtipoimpuesto', '=', 'ti.idtipoimpuesto')
            ->select('prod.idproducto', 'ti.porcentaje')
            ->where('prod.idproducto', $idproducto)
            ->first();

        if (! $producto) {
            throw new Exception("Producto con ID {$idproducto} no encontrado.");
        }

        $porcentaje = (int) $producto->porcentaje;
        $montoitems = (int) round($cantidad * $precioCompra);
        $iva10 = 0;
        $iva5 = 0;
        $gravada10 = 0;
        $gravada5 = 0;
        $exenta = 0;

        if ($porcentaje === 10) {
            $iva10 = (int) round($montoitems / ((100 + $porcentaje) / $porcentaje));
            $gravada10 = $montoitems - $iva10;
        } elseif ($porcentaje === 5) {
            $iva5 = (int) round($montoitems / ((100 + $porcentaje) / $porcentaje));
            $gravada5 = $montoitems - $iva5;
        } else {
            $exenta = $montoitems;
        }

        return [
            'items' => $items,
            'idproducto' => $idproducto,
            'cantidad' => (int) $cantidad,
            'precio_compra' => (int) $precioCompra,
            'iva10' => $iva10,
            'iva5' => $iva5,
            'gravada10' => $gravada10,
            'gravada5' => $gravada5,
            'exenta' => $exenta,
            'montoitems' => $montoitems,
        ];
    }

    private function registrarSalidaStockNotaCredito(
        int $idsucursal,
        int $iddeposito,
        int $idproducto,
        float $cantidad,
        int $idnotaCredito
    ): void {
        DB::table('stock')
            ->where('idsucursal', '=', $idsucursal)
            ->where('iddeposito', '=', $iddeposito)
            ->where('idproducto', '=', $idproducto)
            ->decrement('cantidad', $cantidad);

        app(MovimientoStockService::class)->registrar(
            $idproducto,
            $idsucursal,
            $iddeposito,
            'NC_COMPRA',
            $idnotaCredito,
            'nota_credito_compra_detalle',
            'SALIDA',
            $cantidad,
            null,
            'Devolucion por nota de credito de compra'
        );
    }

    private function registrarEntradaStockNotaCredito(
        int $idsucursal,
        int $iddeposito,
        int $idproducto,
        float $cantidad,
        int $idnotaCredito
    ): void {
        $updated = DB::table('stock')
            ->where('idsucursal', '=', $idsucursal)
            ->where('iddeposito', '=', $iddeposito)
            ->where('idproducto', '=', $idproducto)
            ->increment('cantidad', $cantidad);

        if ($updated === 0) {
            DB::table('stock')->insert([
                'idsucursal' => $idsucursal,
                'iddeposito' => $iddeposito,
                'idproducto' => $idproducto,
                'cantidad' => $cantidad,
            ]);
        }

        app(MovimientoStockService::class)->registrar(
            $idproducto,
            $idsucursal,
            $iddeposito,
            'NC_COMPRA',
            $idnotaCredito,
            'nota_credito_compra_detalle',
            'ENTRADA',
            $cantidad,
            null,
            'Anulacion de nota de credito de compra'
        );
    }

    private function ajustarCuentaPagar(int $idcompra, int $montoNota): void
    {
        DB::table('cuentas_a_pagar')
            ->where('idcompra', '=', $idcompra)
            ->decrement('montoapagar', $montoNota);

        $this->normalizarEstadoCuentaPagar($idcompra);
    }

    private function normalizarEstadoCuentaPagar(int $idcompra): void
    {
        $cuenta = DB::table('cuentas_a_pagar')
            ->where('idcompra', '=', $idcompra)
            ->lockForUpdate()
            ->first();

        if (! $cuenta) {
            return;
        }

        $estado = (int) $cuenta->montoapagar <= 0 ? 'Pagado' : 'Pendiente';

        DB::table('cuentas_a_pagar')
            ->where('idcompra', '=', $idcompra)
            ->update([
                'montoapagar' => max(0, (int) $cuenta->montoapagar),
                'estado' => $estado,
            ]);
    }

    private function actualizarLibroCompras(int $idcompra): void
    {
        app(LibroComprasService::class)->recalcular($idcompra);
    }

    private function estadoEsCancelado(?string $estado): bool
    {
        return in_array(strtoupper(trim((string) $estado)), ['CANCELADO', 'CANCELADA', 'ANULADO', 'ANULADA', 'A'], true);
    }
}
