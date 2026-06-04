<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComprasFormRequest;
use App\Models\Compras;
use App\Models\ComprasDetalle;
use App\Services\LibroComprasService;
use App\Services\MovimientoStockService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class CompraController extends Controller
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
        $queryFactura = trim((string) $request->get('searchText5'));
        $queryRuc = trim((string) $request->get('searchText6'));
        $queryEstado = trim((string) $request->get('searchText7'));

        $queryBuilder = DB::table('compras as c')
            ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'c.iddeposito', '=', 'dep.iddeposito')
            ->join('proveedores as p', 'c.idproveedor', '=', 'p.idproveedor')
            ->join('users as u', 'c.idusuario', '=', 'u.id')
            ->leftJoin('orden_compras as oc', 'c.idordencompra', '=', 'oc.idordencompra')
            ->select(
                'c.idcompra',
                's.descripcion as sucursal',
                'dep.descripcion as deposito',
                'p.razonsocial as proveedor',
                'p.ruc',
                'oc.idordencompra',
                'c.fecha',
                'c.montocompra',
                'c.timbrado',
                'c.condicion',
                'c.concepto',
                'c.nro_factura',
                'c.fecha_factura',
                'c.fecha_vencimiento',
                'u.name as usuario',
                'c.estado'
            );

        if ($query !== '') {
            $queryBuilder->where('c.idcompra', 'LIKE', '%' . $query . '%');
        }

        if ($queryFecha !== '') {
            $queryBuilder->where('c.fecha', 'LIKE', '%' . $queryFecha . '%');
        }

        if ($querySucursal !== '') {
            $queryBuilder->where('s.descripcion', 'LIKE', '%' . $querySucursal . '%');
        }

        if ($queryProveedor !== '') {
            $queryBuilder->where('p.razonsocial', 'LIKE', '%' . $queryProveedor . '%');
        }

        if ($queryFactura !== '') {
            $queryBuilder->where('c.nro_factura', 'LIKE', '%' . $queryFactura . '%');
        }

        if ($queryRuc !== '') {
            $queryBuilder->where('p.ruc', 'LIKE', '%' . $queryRuc . '%');
        }

        if ($queryEstado !== '') {
            $queryBuilder->where('c.estado', 'LIKE', '%' . $queryEstado . '%');
        }

        $compras = $queryBuilder
            ->orderByDesc('c.idcompra')
            ->paginate(7);

        return view('compras.compra.index', [
            'compras' => $compras,
            'searchText' => $query,
            'searchText2' => $queryProveedor,
            'searchText3' => $queryFecha,
            'searchText4' => $querySucursal,
            'searchText5' => $queryFactura,
            'searchText6' => $queryRuc,
            'searchText7' => $queryEstado,
            'total' => $compras->total(),
        ]);
    }

    public function create()
    {
        $suc = Auth::user()->trabaja_sucursal;
        $fecha = date('Y-m-d');

        $ordenes = DB::table('orden_compras as oc')
            ->join('sucursales as s', 'oc.idsucursal', '=', 's.idsucursal')
            ->join('proveedores as p', 'oc.idproveedor', '=', 'p.idproveedor')
            ->join('depositos as dep', 'oc.iddeposito', '=', 'dep.iddeposito')
            ->select(
                'oc.idordencompra',
                's.idsucursal',
                's.descripcion as sucursal',
                'p.idproveedor',
                'p.razonsocial',
                'p.ruc',
                'dep.descripcion as deposito',
                'oc.fecha',
                'oc.estado'
            )
            ->where('oc.idsucursal', '=', $suc)
            ->where('oc.estado', '=', 'Pendiente')
            ->whereNotExists(function ($subquery): void {
                $subquery->select(DB::raw(1))
                    ->from('compras as c')
                    ->whereColumn('c.idordencompra', 'oc.idordencompra')
                    ->whereNotIn('c.estado', ['Cancelado', 'Anulado', 'Anulada', 'A']);
            })
            ->orderByDesc('oc.idordencompra')
            ->get();

        $sucursal = DB::table('sucursales')->where('idsucursal', '=', $suc)->first();

        return view('compras.compra.create', compact('fecha', 'ordenes', 'sucursal'));
    }

    public function store(ComprasFormRequest $request)
    {
        return Redirect::route('compra.create')
            ->with('error', 'Debe seleccionar una orden de compra para registrar la compra.');
    }

    public function insertar_ordenes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero_orden' => 'required|integer|exists:orden_compras,idordencompra',
            'fecha' => 'required|date',
            'fecha_factura' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_factura',
            'nro_factura' => 'required|string|max:30',
            'timbrado' => 'required|string|max:25',
            'condicion' => 'required|string|max:30',
            'concepto' => 'nullable|string|max:100',
        ], [
            'numero_orden.required' => 'Debe seleccionar una orden de compra.',
            'nro_factura.required' => 'Debe ingresar el numero de factura.',
            'timbrado.required' => 'Debe ingresar el timbrado.',
            'fecha_factura.required' => 'Debe seleccionar la fecha de factura.',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la fecha de factura.',
            'condicion.required' => 'Debe seleccionar la condicion de compra.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $orden = DB::table('orden_compras as oc')
                ->join('proveedores as p', 'oc.idproveedor', '=', 'p.idproveedor')
                ->select(
                    'oc.idordencompra',
                    'oc.idsucursal',
                    'oc.idproveedor',
                    'oc.iddeposito',
                    'oc.estado',
                    'p.ruc'
                )
                ->where('oc.idordencompra', '=', $request->input('numero_orden'))
                ->lockForUpdate()
                ->first();

            if (! $orden || $this->estadoEsCancelado($orden->estado)) {
                throw new Exception('La orden de compra no existe o se encuentra cancelada.');
            }

            if ($this->normalizarTexto((string) $orden->estado) !== 'PENDIENTE') {
                throw new Exception('La orden seleccionada no se encuentra pendiente para registrar la compra.');
            }

            if ((int) $orden->idsucursal !== (int) Auth::user()->trabaja_sucursal) {
                throw new Exception('La orden seleccionada no pertenece a la sucursal actual.');
            }

            if (! $orden->iddeposito) {
                throw new Exception('La orden seleccionada no posee deposito asociado.');
            }

            $existeCompra = DB::table('compras')
                ->where('idordencompra', '=', $orden->idordencompra)
                ->whereNotIn('estado', ['Cancelado', 'Anulado', 'Anulada', 'A'])
                ->exists();

            if ($existeCompra) {
                throw new Exception('La orden seleccionada ya posee una compra activa.');
            }

            $this->validarFacturaDuplicada(
                (int) $orden->idproveedor,
                (string) $request->input('timbrado'),
                (string) $request->input('nro_factura')
            );

            $compra = Compras::create([
                'idproveedor' => $orden->idproveedor,
                'idordencompra' => $orden->idordencompra,
                'idsucursal' => $orden->idsucursal,
                'iddeposito' => $orden->iddeposito,
                'idusuario' => Auth::id(),
                'nro_factura' => $request->input('nro_factura'),
                'condicion' => $request->input('condicion'),
                'concepto' => $request->input('concepto'),
                'timbrado' => $request->input('timbrado'),
                'ruc' => $orden->ruc,
                'montoiva10' => 0,
                'montoiva5' => 0,
                'montogravada10' => 0,
                'montogravada5' => 0,
                'montoexenta' => 0,
                'montocompra' => 0,
                'fecha' => $request->input('fecha'),
                'fecha_factura' => $request->input('fecha_factura'),
                'fecha_vencimiento' => $request->input('fecha_vencimiento'),
                'estado' => 'Pendiente',
            ]);

            $ordenDetalles = DB::table('orden_detalle')
                ->where('idordencompra', '=', $orden->idordencompra)
                ->orderBy('items')
                ->get();

            if ($ordenDetalles->isEmpty()) {
                throw new Exception('La orden seleccionada no posee detalle de productos.');
            }

            foreach ($ordenDetalles as $detalle) {
                ComprasDetalle::create([
                    'idcompra' => $compra->idcompra,
                    'items' => $detalle->items,
                    'idproducto' => $detalle->idproducto,
                    'cantidad' => $detalle->cantidad,
                    'precio_compra' => $detalle->precio_compra,
                    'iva10' => 0,
                    'iva5' => 0,
                    'gravada10' => 0,
                    'gravada5' => 0,
                    'exenta' => 0,
                    'montoitems' => 0,
                ]);
            }

            DB::commit();

            return Redirect::route('compra.edit', $compra->idcompra)
                ->with('success', 'Orden cargada correctamente. Verifique el detalle y presione Actualizar.');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $datos = $this->datosCompra((int) $id);

        if (! $datos['compra']) {
            abort(404);
        }

        return view('compras.compra.show', $datos);
    }

    public function edit($id)
    {
        $datos = $this->datosCompra((int) $id);

        if (! $datos['compra']) {
            abort(404);
        }

        if ($this->estadoEsCancelado($datos['compra']->estado)) {
            return Redirect::route('compra.show', $id)
                ->with('error', 'No se puede modificar una compra cancelada.');
        }

        return view('compras.compra.edit', $datos);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'idcompra_detalle' => 'required|array|min:1',
            'idcompra_detalle.*' => 'required|integer|exists:compra_detalle,idcompra_detalle',
            'idproducto' => 'required|array',
            'cantidad' => 'required|array',
            'precio_compra' => 'required|array',
            'cantidad.*' => 'required|numeric|min:1',
            'precio_compra.*' => 'required|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            $compra = Compras::where('idcompra', (int) $id)->lockForUpdate()->firstOrFail();

            if ($this->estadoEsCancelado($compra->estado)) {
                throw new Exception('No se puede modificar una compra cancelada.');
            }

            $yaAplicada = $this->compraTieneStockAplicado((int) $compra->idcompra);
            $idsDetalle = $request->input('idcompra_detalle', []);
            $cantidades = $request->input('cantidad', []);
            $precios = $request->input('precio_compra', []);
            $productos = $request->input('idproducto', []);

            $sumiva10 = 0;
            $sumiva5 = 0;
            $sumgravada10 = 0;
            $sumgravada5 = 0;
            $sumexenta = 0;
            $summontoitems = 0;
            $items = 1;

            foreach ($idsDetalle as $detalleId) {
                $detalleId = (int) $detalleId;
                $detalle = ComprasDetalle::where('idcompra_detalle', $detalleId)
                    ->where('idcompra', (int) $compra->idcompra)
                    ->lockForUpdate()
                    ->firstOrFail();

                $idproducto = (int) ($productos[$detalleId] ?? $detalle->idproducto);
                $cantidadNueva = (float) ($cantidades[$detalleId] ?? 0);
                $precioCompra = (float) ($precios[$detalleId] ?? 0);

                if ((int) $detalle->idproducto !== $idproducto) {
                    throw new Exception('Uno de los productos no corresponde al detalle de la compra.');
                }

                if ($cantidadNueva <= 0 || $precioCompra <= 0) {
                    throw new Exception('La cantidad y el precio de compra deben ser mayores a cero.');
                }

                $linea = $this->calcularLineaCompra($idproducto, $cantidadNueva, $precioCompra, $items);
                $cantidadAnterior = (float) $detalle->cantidad;
                $deltaStock = $yaAplicada ? ($cantidadNueva - $cantidadAnterior) : $cantidadNueva;

                if ($deltaStock > 0) {
                    $this->registrarEntradaStockCompra(
                        (int) $compra->idsucursal,
                        (int) $compra->iddeposito,
                        $idproducto,
                        $deltaStock,
                        (int) $compra->idcompra,
                        $precioCompra,
                        $yaAplicada ? 'Ajuste por modificacion de compra' : 'Ingreso por compra'
                    );
                } elseif ($deltaStock < 0) {
                    $this->registrarSalidaStockCompra(
                        (int) $compra->idsucursal,
                        (int) $compra->iddeposito,
                        $idproducto,
                        abs($deltaStock),
                        (int) $compra->idcompra,
                        'Reversion parcial por modificacion de compra'
                    );
                }

                $detalle->forceFill($linea);
                $detalle->save();

                $sumiva10 += $linea['iva10'];
                $sumiva5 += $linea['iva5'];
                $sumgravada10 += $linea['gravada10'];
                $sumgravada5 += $linea['gravada5'];
                $sumexenta += $linea['exenta'];
                $summontoitems += $linea['montoitems'];
                $items++;
            }

            $compra->forceFill([
                'montoiva10' => $sumiva10,
                'montoiva5' => $sumiva5,
                'montogravada10' => $sumgravada10,
                'montogravada5' => $sumgravada5,
                'montoexenta' => $sumexenta,
                'montocompra' => $summontoitems,
                'estado' => 'Realizado',
            ]);
            $compra->save();

            $this->sincronizarCuentaPagar($compra);
            $this->registrarLibroCompra($compra);

            if ($compra->idordencompra) {
                DB::table('orden_compras')
                    ->where('idordencompra', '=', $compra->idordencompra)
                    ->update(['estado' => 'Realizado']);
            }

            DB::commit();

            return Redirect::route('compra.show', $compra->idcompra)
                ->with('success', 'Operacion exitosa.');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $compra = Compras::where('idcompra', (int) $id)->lockForUpdate()->firstOrFail();

            if ($this->estadoEsCancelado($compra->estado)) {
                DB::rollBack();

                return Redirect::route('compra.index')->with('info', 'La compra ya estaba cancelada.');
            }

            if ($this->compraTieneStockAplicado((int) $compra->idcompra)) {
                $detalles = ComprasDetalle::where('idcompra', (int) $compra->idcompra)->lockForUpdate()->get();

                foreach ($detalles as $detalle) {
                    $this->registrarSalidaStockCompra(
                        (int) $compra->idsucursal,
                        (int) $compra->iddeposito,
                        (int) $detalle->idproducto,
                        (float) $detalle->cantidad,
                        (int) $compra->idcompra,
                        'Anulacion de compra'
                    );
                }
            }

            DB::table('cuentas_a_pagar')
                ->where('idcompra', (int) $compra->idcompra)
                ->update([
                    'montoapagar' => 0,
                    'montopagado' => 0,
                    'estado' => 'Cancelado',
                ]);

            $compra->estado = 'Cancelado';
            $compra->save();

            app(LibroComprasService::class)->recalcular((int) $compra->idcompra);

            if ($compra->idordencompra) {
                DB::table('orden_compras')
                    ->where('idordencompra', '=', $compra->idordencompra)
                    ->update(['estado' => 'Pendiente']);
            }

            DB::commit();

            return Redirect::route('compra.index')->with('success', 'Compra cancelada correctamente.');
        } catch (Exception $e) {
            DB::rollBack();

            return Redirect::route('compra.index')->with('error', 'Error al cancelar la compra: ' . $e->getMessage());
        }
    }

    private function datosCompra(int $id): array
    {
        $compra = DB::table('compras as c')
            ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'c.iddeposito', '=', 'dep.iddeposito')
            ->join('proveedores as p', 'c.idproveedor', '=', 'p.idproveedor')
            ->join('users as u', 'c.idusuario', '=', 'u.id')
            ->leftJoin('orden_compras as oc', 'c.idordencompra', '=', 'oc.idordencompra')
            ->select(
                'c.idcompra',
                'u.name as usuario',
                's.descripcion as sucursal',
                'dep.descripcion as deposito',
                'p.idproveedor',
                'p.razonsocial as proveedor',
                'p.ruc',
                'c.idordencompra',
                'c.fecha',
                'c.montoiva10',
                'c.montoiva5',
                'c.montogravada10',
                'c.montogravada5',
                'c.montoexenta',
                'c.montocompra',
                'c.timbrado',
                'c.condicion',
                'c.concepto',
                'c.nro_factura',
                'c.estado',
                'c.fecha_factura',
                'c.fecha_vencimiento'
            )
            ->where('c.idcompra', '=', $id)
            ->first();

        $detalles = DB::table('compra_detalle as d')
            ->join('productos as prod', 'd.idproducto', '=', 'prod.idproducto')
            ->select(
                'd.idcompra_detalle',
                'prod.idproducto',
                'prod.descripcion as producto',
                'd.cantidad',
                'd.precio_compra',
                'd.iva10',
                'd.iva5',
                'd.gravada10',
                'd.gravada5',
                'd.exenta',
                'd.montoitems'
            )
            ->where('d.idcompra', '=', $id)
            ->orderBy('d.items')
            ->get();

        return compact('compra', 'detalles');
    }

    private function calcularLineaCompra(int $idproducto, float $cantidad, float $precioCompra, int $items): array
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
            'idproducto' => $idproducto,
            'cantidad' => (int) $cantidad,
            'precio_compra' => (int) $precioCompra,
            'items' => $items,
            'iva10' => $iva10,
            'iva5' => $iva5,
            'gravada10' => $gravada10,
            'gravada5' => $gravada5,
            'exenta' => $exenta,
            'montoitems' => $montoitems,
        ];
    }

    private function compraTieneStockAplicado(int $idcompra): bool
    {
        if (DB::table('movimiento_stock')
            ->where('tipo_origen', 'COMPRA')
            ->where('id_origen', $idcompra)
            ->exists()) {
            return true;
        }

        return DB::table('compras')
            ->where('idcompra', $idcompra)
            ->where('montocompra', '>', 0)
            ->whereNotIn('estado', ['Pendiente'])
            ->exists();
    }

    private function registrarEntradaStockCompra(
        int $idsucursal,
        int $iddeposito,
        int $idproducto,
        float $cantidad,
        int $idcompra,
        float $costoUnitario,
        string $observacion
    ): void {
        $baseStock = DB::table('stock')
            ->where('idsucursal', $idsucursal)
            ->where('iddeposito', $iddeposito)
            ->where('idproducto', $idproducto);

        $updated = (clone $baseStock)->increment('cantidad', $cantidad);

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
            'COMPRA',
            $idcompra,
            'compra_detalle',
            'ENTRADA',
            $cantidad,
            $costoUnitario,
            $observacion
        );
    }

    private function registrarSalidaStockCompra(
        int $idsucursal,
        int $iddeposito,
        int $idproducto,
        float $cantidad,
        int $idcompra,
        string $observacion
    ): void {
        $stock = DB::table('stock')
            ->where('idsucursal', $idsucursal)
            ->where('iddeposito', $iddeposito)
            ->where('idproducto', $idproducto)
            ->lockForUpdate()
            ->first();

        if (! $stock || (float) $stock->cantidad < $cantidad) {
            $producto = DB::table('productos')->where('idproducto', $idproducto)->value('descripcion') ?? $idproducto;
            $disponible = $stock ? (float) $stock->cantidad : 0;

            throw new Exception("No se puede revertir stock para {$producto}. Disponible: {$disponible}, requerido: {$cantidad}.");
        }

        DB::table('stock')
            ->where('idsucursal', $idsucursal)
            ->where('iddeposito', $iddeposito)
            ->where('idproducto', $idproducto)
            ->decrement('cantidad', $cantidad);

        app(MovimientoStockService::class)->registrar(
            $idproducto,
            $idsucursal,
            $iddeposito,
            'COMPRA',
            $idcompra,
            'compra_detalle',
            'SALIDA',
            $cantidad,
            null,
            $observacion
        );
    }

    private function sincronizarCuentaPagar(Compras $compra): void
    {
        $monto = (int) $compra->montocompra;
        $esCredito = str_contains($this->normalizarTexto((string) $compra->condicion), 'CREDITO');

        DB::table('cuentas_a_pagar')->updateOrInsert(
            ['idcompra' => (int) $compra->idcompra],
            [
                'idproveedor' => (int) $compra->idproveedor,
                'idsucursal' => (int) $compra->idsucursal,
                'fecha_vencimiento' => $compra->fecha_vencimiento,
                'fecha_factura' => $compra->fecha_factura,
                'montopagado' => $esCredito ? 0 : $monto,
                'montoapagar' => $esCredito ? $monto : 0,
                'estado' => $esCredito ? 'Pendiente' : 'Pagado',
            ]
        );
    }

    private function registrarLibroCompra(Compras $compra): void
    {
        app(LibroComprasService::class)->recalcular((int) $compra->idcompra);
    }

    private function validarFacturaDuplicada(int $idproveedor, string $timbrado, string $nroFactura, ?int $idcompra = null): void
    {
        $query = DB::table('compras')
            ->where('idproveedor', $idproveedor)
            ->where('timbrado', $timbrado)
            ->where('nro_factura', $nroFactura)
            ->whereNotIn('estado', ['Cancelado', 'Anulado', 'Anulada', 'A']);

        if ($idcompra !== null) {
            $query->where('idcompra', '<>', $idcompra);
        }

        if ($query->exists()) {
            throw new Exception('Ya existe una compra activa con el mismo proveedor, timbrado y numero de factura.');
        }
    }

    private function estadoEsCancelado(?string $estado): bool
    {
        return in_array(strtoupper(trim((string) $estado)), ['CANCELADO', 'CANCELADA', 'ANULADO', 'ANULADA', 'A'], true);
    }

    private function normalizarTexto(string $texto): string
    {
        return strtr(strtoupper($texto), [
            'Á' => 'A',
            'É' => 'E',
            'Í' => 'I',
            'Ó' => 'O',
            'Ú' => 'U',
        ]);
    }
}
