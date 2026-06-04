<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotaDebitoCRequest;
use App\Models\NotaDebitoC;
use App\Models\NotaDebitocDetalle;
use App\Services\LibroComprasService;
use App\Services\MovimientoStockService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class NotaDebitoCController extends Controller
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

        $builder = DB::table('nota_debito_compra as nd')
            ->join('compras as c', 'nd.idcompra', '=', 'c.idcompra')
            ->join('sucursales as s', 'nd.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'nd.iddeposito', '=', 'dep.iddeposito')
            ->join('proveedores as p', 'nd.idproveedor', '=', 'p.idproveedor')
            ->join('users as u', 'nd.idusuario', '=', 'u.id')
            ->select(
                'nd.idnota_debitoc',
                'nd.idcompra',
                'nd.nro_nota_debito',
                'u.name as usuario',
                's.descripcion as sucursal',
                'dep.descripcion as deposito',
                'p.razonsocial as proveedor',
                'p.ruc as num_documento',
                'nd.fecha_registro',
                'nd.montoiva10',
                'nd.montoiva5',
                'nd.montogravada10',
                'nd.montogravada5',
                'nd.montoexenta',
                'nd.montonota_debito_compra',
                'nd.timbrado',
                'nd.concepto',
                'nd.estado',
                'nd.mueve_stock',
                'nd.fecha_factura',
                'nd.fecha_vencimiento',
                'c.nro_factura as nro_factura_compra'
            );

        if ($query !== '') {
            $builder->where('nd.idnota_debitoc', 'LIKE', '%' . $query . '%');
        }

        if ($queryProveedor !== '') {
            $builder->where('p.razonsocial', 'LIKE', '%' . $queryProveedor . '%');
        }

        if ($queryFecha !== '') {
            $builder->where('nd.fecha_registro', 'LIKE', '%' . $queryFecha . '%');
        }

        if ($querySucursal !== '') {
            $builder->where('s.descripcion', 'LIKE', '%' . $querySucursal . '%');
        }

        if ($queryComprobante !== '') {
            $builder->where('nd.nro_nota_debito', 'LIKE', '%' . $queryComprobante . '%');
        }

        if ($queryRuc !== '') {
            $builder->where('p.ruc', 'LIKE', '%' . $queryRuc . '%');
        }

        $nota_debitoc = $builder->orderByDesc('nd.idnota_debitoc')->paginate(7);

        return view('compras.nota_debitoc.index', [
            'nota_debitoc' => $nota_debitoc,
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
            return Redirect::route('nota_debitoc.create')
                ->with('error', 'La compra seleccionada no existe o no esta disponible para nota de debito.');
        }

        $compras = $this->comprasDisponibles($suc);
        $sucursal = DB::table('sucursales')->where('idsucursal', $suc)->first();
        $detalles = $compra ? $this->detallesCompra((int) $compra->idcompra) : collect();

        return view('compras.nota_debitoc.create', [
            'compras' => $compras,
            'compra' => $compra,
            'detalles' => $detalles,
            'sucursal' => $sucursal,
        ]);
    }

    public function store(NotaDebitoCRequest $request)
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
                (string) $request->input('nro_nota_debito')
            );

            $mueveStock = $request->boolean('mueve_stock');
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

                $this->validarProductoCompra((int) $compra->idcompra, $idproducto, $cantidad);

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

            $notaDebito = NotaDebitoC::create([
                'idcompra' => (int) $compra->idcompra,
                'idsucursal' => (int) $compra->idsucursal,
                'iddeposito' => (int) $compra->iddeposito,
                'idproveedor' => (int) $compra->idproveedor,
                'idusuario' => Auth::id(),
                'ruc' => (string) $compra->ruc,
                'nro_nota_debito' => $request->input('nro_nota_debito'),
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
                'montonota_debito_compra' => $summontoitems,
                'mueve_stock' => $mueveStock,
                'estado' => 'Realizado',
            ]);

            foreach ($lineas as $linea) {
                NotaDebitocDetalle::create(array_merge($linea, [
                    'idnota_debitoc' => (int) $notaDebito->idnota_debitoc,
                ]));

                if ($mueveStock) {
                    $this->registrarEntradaStockNotaDebito(
                        (int) $compra->idsucursal,
                        (int) $compra->iddeposito,
                        (int) $linea['idproducto'],
                        (float) $linea['cantidad'],
                        (int) $notaDebito->idnota_debitoc
                    );
                }
            }

            $this->aumentarCuentaPagar((int) $compra->idcompra, $summontoitems);
            app(LibroComprasService::class)->recalcular((int) $compra->idcompra);

            DB::commit();

            return Redirect::route('nota_debitoc.show', $notaDebito->idnota_debitoc)
                ->with('success', 'Operacion exitosa.');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $datos = $this->datosNotaDebito((int) $id);

        if (! $datos['nota_debitoc']) {
            abort(404);
        }

        return view('compras.nota_debitoc.show', $datos);
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $notaDebito = NotaDebitoC::where('idnota_debitoc', (int) $id)->lockForUpdate()->firstOrFail();

            if ($this->estadoEsCancelado($notaDebito->estado)) {
                DB::rollBack();

                return Redirect::route('nota_debitoc.index')->with('info', 'La nota de debito ya estaba cancelada.');
            }

            $detalles = NotaDebitocDetalle::where('idnota_debitoc', (int) $notaDebito->idnota_debitoc)
                ->lockForUpdate()
                ->get();

            if ((bool) $notaDebito->mueve_stock) {
                foreach ($detalles as $detalle) {
                    $this->registrarSalidaStockNotaDebito(
                        (int) $notaDebito->idsucursal,
                        (int) $notaDebito->iddeposito,
                        (int) $detalle->idproducto,
                        (float) $detalle->cantidad,
                        (int) $notaDebito->idnota_debitoc
                    );
                }
            }

            $this->revertirCuentaPagar((int) $notaDebito->idcompra, (int) $notaDebito->montonota_debito_compra);

            $notaDebito->estado = 'Cancelado';
            $notaDebito->save();

            app(LibroComprasService::class)->recalcular((int) $notaDebito->idcompra);

            DB::commit();

            return Redirect::route('nota_debitoc.index')
                ->with('success', 'Nota de debito anulada correctamente.');
        } catch (Exception $e) {
            DB::rollBack();

            return Redirect::route('nota_debitoc.index')
                ->with('error', 'Error al anular la nota de debito: ' . $e->getMessage());
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

    private function detallesCompra(int $idcompra)
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
            ->get();
    }

    private function datosNotaDebito(int $id): array
    {
        $nota_debitoc = DB::table('nota_debito_compra as nd')
            ->join('compras as c', 'nd.idcompra', '=', 'c.idcompra')
            ->join('sucursales as s', 'nd.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'nd.iddeposito', '=', 'dep.iddeposito')
            ->join('proveedores as p', 'nd.idproveedor', '=', 'p.idproveedor')
            ->join('users as u', 'nd.idusuario', '=', 'u.id')
            ->select(
                'nd.idnota_debitoc',
                'nd.idcompra',
                'nd.nro_nota_debito',
                'u.name as usuario',
                's.descripcion as sucursal',
                'dep.descripcion as deposito',
                'p.razonsocial as proveedor',
                'p.ruc as num_documento',
                'nd.fecha_registro',
                'nd.montoiva10',
                'nd.montoiva5',
                'nd.montogravada10',
                'nd.montogravada5',
                'nd.montoexenta',
                'nd.montonota_debito_compra',
                'nd.timbrado',
                'nd.concepto',
                'nd.estado',
                'nd.mueve_stock',
                'nd.fecha_factura',
                'nd.fecha_vencimiento',
                'c.nro_factura as nro_factura_compra'
            )
            ->where('nd.idnota_debitoc', '=', $id)
            ->first();

        $detalles = DB::table('nota_debito_compra_detalle as d')
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
            ->where('d.idnota_debitoc', '=', $id)
            ->orderBy('d.items')
            ->get();

        return compact('nota_debitoc', 'detalles');
    }

    private function validarProductoCompra(int $idcompra, int $idproducto, float $cantidad): void
    {
        if ($cantidad <= 0) {
            throw new Exception('La cantidad debe ser mayor a cero.');
        }

        $existe = DB::table('compra_detalle')
            ->where('idcompra', '=', $idcompra)
            ->where('idproducto', '=', $idproducto)
            ->exists();

        if (! $existe) {
            throw new Exception('El producto seleccionado no pertenece a la compra original.');
        }
    }

    private function validarComprobanteDuplicado(int $idproveedor, string $timbrado, string $nroNota): void
    {
        $existe = DB::table('nota_debito_compra')
            ->where('idproveedor', '=', $idproveedor)
            ->where('timbrado', '=', $timbrado)
            ->where('nro_nota_debito', '=', $nroNota)
            ->whereNotIn('estado', ['Cancelado', 'Anulado', 'Anulada', 'A'])
            ->exists();

        if ($existe) {
            throw new Exception('Ya existe una nota de debito activa con el mismo proveedor, timbrado y numero.');
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

    private function registrarEntradaStockNotaDebito(
        int $idsucursal,
        int $iddeposito,
        int $idproducto,
        float $cantidad,
        int $idnotaDebito
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
            'ND_COMPRA',
            $idnotaDebito,
            'nota_debito_compra_detalle',
            'ENTRADA',
            $cantidad,
            null,
            'Ingreso por nota de debito de compra'
        );
    }

    private function registrarSalidaStockNotaDebito(
        int $idsucursal,
        int $iddeposito,
        int $idproducto,
        float $cantidad,
        int $idnotaDebito
    ): void {
        $stock = DB::table('stock')
            ->where('idsucursal', '=', $idsucursal)
            ->where('iddeposito', '=', $iddeposito)
            ->where('idproducto', '=', $idproducto)
            ->lockForUpdate()
            ->first();

        if (! $stock || (float) $stock->cantidad < $cantidad) {
            $producto = DB::table('productos')->where('idproducto', '=', $idproducto)->value('descripcion') ?? $idproducto;
            $disponible = $stock ? (float) $stock->cantidad : 0;

            throw new Exception("No existe stock suficiente para anular la nota de debito de {$producto}. Disponible: {$disponible}, requerido: {$cantidad}.");
        }

        DB::table('stock')
            ->where('idsucursal', '=', $idsucursal)
            ->where('iddeposito', '=', $iddeposito)
            ->where('idproducto', '=', $idproducto)
            ->decrement('cantidad', $cantidad);

        app(MovimientoStockService::class)->registrar(
            $idproducto,
            $idsucursal,
            $iddeposito,
            'ND_COMPRA',
            $idnotaDebito,
            'nota_debito_compra_detalle',
            'SALIDA',
            $cantidad,
            null,
            'Anulacion de nota de debito de compra'
        );
    }

    private function aumentarCuentaPagar(int $idcompra, int $montoNota): void
    {
        DB::table('cuentas_a_pagar')
            ->where('idcompra', '=', $idcompra)
            ->increment('montoapagar', $montoNota);

        $this->normalizarEstadoCuentaPagar($idcompra);
    }

    private function revertirCuentaPagar(int $idcompra, int $montoNota): void
    {
        DB::table('cuentas_a_pagar')
            ->where('idcompra', '=', $idcompra)
            ->update([
                'montoapagar' => DB::raw('GREATEST(0, montoapagar - ' . (int) $montoNota . ')'),
            ]);

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

        DB::table('cuentas_a_pagar')
            ->where('idcompra', '=', $idcompra)
            ->update([
                'montoapagar' => max(0, (int) $cuenta->montoapagar),
                'estado' => (int) $cuenta->montoapagar <= 0 ? 'Pagado' : 'Pendiente',
            ]);
    }

    private function estadoEsCancelado(?string $estado): bool
    {
        return in_array(strtoupper(trim((string) $estado)), ['CANCELADO', 'CANCELADA', 'ANULADO', 'ANULADA', 'A'], true);
    }
}
