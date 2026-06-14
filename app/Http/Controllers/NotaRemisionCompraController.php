<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotaRemisionCompraFormRequest;
use App\Models\NotaRemisionCompra;
use App\Models\NotaRemisionCompraDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class NotaRemisionCompraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = trim((string) $request->get('searchText'));
        $query2 = trim((string) $request->get('searchText2'));
        $query3 = trim((string) $request->get('searchText3'));
        $query4 = trim((string) $request->get('searchText4'));
        $query5 = trim((string) $request->get('searchText5'));
        $query6 = trim((string) $request->get('searchText6'));

        $queryBuilder = DB::table('nota_remision_compra as nr')
            ->join('orden_compras as oc', 'nr.idordencompra', '=', 'oc.idordencompra')
            ->join('proveedores as p', 'nr.idproveedor', '=', 'p.idproveedor')
            ->join('depositos as dep', 'nr.iddeposito', '=', 'dep.iddeposito')
            ->join('sucursales as s', 'nr.idsucursal', '=', 's.idsucursal')
            ->join('users as u', 'nr.idusuario', '=', 'u.id')
            ->select(
                'nr.idremisionc',
                'nr.idordencompra',
                'nr.nro_comprobante',
                'nr.fecha',
                'nr.fecha_remision',
                'nr.motivo_traslado',
                'nr.chofer',
                'nr.vehiculo',
                'nr.chapa',
                'nr.estado',
                'u.name as usuario',
                'p.razonsocial as proveedor',
                'p.ruc',
                'dep.descripcion as deposito',
                's.descripcion as sucursal'
            );

        if ($query !== '') {
            $queryBuilder->where('nr.idremisionc', 'LIKE', '%' . $query . '%');
        }

        if ($query2 !== '') {
            $queryBuilder->where('nr.fecha_remision', 'LIKE', '%' . $query2 . '%');
        }

        if ($query3 !== '') {
            $queryBuilder->where('p.razonsocial', 'LIKE', '%' . $query3 . '%');
        }

        if ($query4 !== '') {
            $queryBuilder->where('nr.nro_comprobante', 'LIKE', '%' . $query4 . '%');
        }

        if ($query5 !== '') {
            $queryBuilder->where('nr.idordencompra', 'LIKE', '%' . $query5 . '%');
        }

        if ($query6 !== '') {
            $queryBuilder->where('nr.estado', 'LIKE', '%' . $query6 . '%');
        }

        $nota_remision = $queryBuilder
            ->orderByDesc('nr.idremisionc')
            ->paginate(7);

        return view('compras.nota_remision.index', [
            'nota_remision' => $nota_remision,
            'searchText' => $query,
            'searchText2' => $query2,
            'searchText3' => $query3,
            'searchText4' => $query4,
            'searchText5' => $query5,
            'searchText6' => $query6,
            'total' => $nota_remision->total(),
        ]);
    }

    public function create(Request $request)
    {
        $suc = Auth::user()->trabaja_sucursal;
        $fecha = date('Y-m-d');
        $ordenSeleccionada = $request->integer('orden');

        $ordenes = DB::table('orden_compras as oc')
            ->join('proveedores as p', 'oc.idproveedor', '=', 'p.idproveedor')
            ->join('depositos as dep', 'oc.iddeposito', '=', 'dep.iddeposito')
            ->select(
                'oc.idordencompra',
                'oc.fecha',
                'oc.estado',
                'p.razonsocial',
                'p.ruc',
                'dep.descripcion as deposito'
            )
            ->where('oc.idsucursal', '=', $suc)
            ->whereNotIn('oc.estado', ['Cancelado', 'Anulado', 'Anulada', 'A'])
            ->whereExists(function ($subquery): void {
                $subquery->select(DB::raw(1))
                    ->from('orden_detalle as od')
                    ->whereColumn('od.idordencompra', 'oc.idordencompra')
                    ->whereRaw(
                        "od.cantidad > COALESCE((
                            SELECT SUM(nrd.cantidad)
                            FROM nota_remision_compra_detalle as nrd
                            INNER JOIN nota_remision_compra as nr ON nrd.idremisionc = nr.idremisionc
                            WHERE nr.idordencompra = oc.idordencompra
                              AND nrd.idorden_detalle = od.idorden_detalle
                              AND nr.estado NOT IN ('Cancelado', 'Anulado', 'Anulada', 'A')
                        ), 0)"
                    );
            })
            ->orderByDesc('oc.idordencompra')
            ->get();

        $sucursal = DB::table('sucursales')->where('idsucursal', '=', $suc)->first();
        $orden = null;
        $detalles = collect();

        if ($ordenSeleccionada) {
            $orden = $this->obtenerOrden($ordenSeleccionada);

            if (!$orden || (int) $orden->idsucursal !== (int) $suc || $this->estaCancelada($orden->estado)) {
                return Redirect::to('compras/nota_remision/create')
                    ->with('error', 'Orden de compra no encontrada o cancelada.');
            }

            $detalles = $this->obtenerDetallesPendientes($ordenSeleccionada);

            if ($detalles->isEmpty()) {
                return Redirect::to('compras/nota_remision/create')
                    ->with('error', 'La orden seleccionada no posee productos pendientes de remision.');
            }
        }

        return view('compras.nota_remision.create', compact('fecha', 'ordenes', 'orden', 'detalles', 'sucursal'));
    }

    public function store(NotaRemisionCompraFormRequest $request)
    {
        try {
            DB::beginTransaction();

            $orden = $this->obtenerOrden((int) $request->input('idordencompra'));

            if (!$orden || $this->estaCancelada($orden->estado)) {
                throw new \RuntimeException('La orden de compra no existe o se encuentra cancelada.');
            }

            if ((int) $orden->idsucursal !== (int) Auth::user()->trabaja_sucursal) {
                throw new \RuntimeException('La orden seleccionada no pertenece a la sucursal actual.');
            }

            $existeComprobante = DB::table('nota_remision_compra')
                ->where('idproveedor', '=', $orden->idproveedor)
                ->where('nro_comprobante', '=', $request->input('nro_comprobante'))
                ->whereNotIn('estado', ['Cancelado', 'Anulado', 'Anulada', 'A'])
                ->exists();

            if ($existeComprobante) {
                throw new \RuntimeException('Ya existe una nota de remision activa con ese comprobante para el proveedor.');
            }

            $detallesOrden = $this->obtenerDetallesPendientes((int) $orden->idordencompra)
                ->keyBy('idorden_detalle');

            if ($detallesOrden->isEmpty()) {
                throw new \RuntimeException('La orden seleccionada no posee productos pendientes de remision.');
            }

            $remision = NotaRemisionCompra::create([
                'idordencompra' => $orden->idordencompra,
                'idproveedor' => $orden->idproveedor,
                'iddeposito' => $orden->iddeposito,
                'idsucursal' => $orden->idsucursal,
                'idusuario' => Auth::id(),
                'nro_comprobante' => $request->input('nro_comprobante'),
                'fecha' => now()->toDateString(),
                'fecha_remision' => $request->input('fecha_remision'),
                'motivo_traslado' => $request->input('motivo_traslado'),
                'chofer' => $request->input('chofer'),
                'documento_chofer' => $request->input('documento_chofer'),
                'vehiculo' => $request->input('vehiculo'),
                'chapa' => $request->input('chapa'),
                'observacion' => $request->input('observacion'),
                'estado' => 'Realizado',
            ]);

            foreach ($request->input('idorden_detalle', []) as $index => $idDetalle) {
                $detalleOrden = $detallesOrden->get((int) $idDetalle);
                $cantidad = (float) ($request->input('cantidad')[$index] ?? 0);
                $idproducto = (int) ($request->input('idproducto')[$index] ?? 0);

                if (!$detalleOrden || (int) $detalleOrden->idproducto !== $idproducto) {
                    throw new \RuntimeException('Uno de los productos no pertenece a la orden seleccionada.');
                }

                if ($cantidad <= 0) {
                    throw new \RuntimeException('La cantidad debe ser mayor a cero.');
                }

                if ($cantidad > (float) $detalleOrden->cantidad_pendiente) {
                    throw new \RuntimeException('La cantidad ingresada supera la cantidad pendiente del producto ' . $detalleOrden->producto . '.');
                }

                NotaRemisionCompraDetalle::create([
                    'idremisionc' => $remision->idremisionc,
                    'idorden_detalle' => $detalleOrden->idorden_detalle,
                    'idproducto' => $detalleOrden->idproducto,
                    'items' => $detalleOrden->items,
                    'cantidad' => $cantidad,
                ]);
            }

            DB::commit();

            return Redirect::route('nota_remision_compra.show', $remision->idremisionc)
                ->with('success', 'Operacion exitosa.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'No se pudo registrar la nota de remision: ' . $e->getMessage());
        }
    }

    public function show(int $idremisionc)
    {
        $datos = $this->datosRemision($idremisionc);

        if (!$datos['remision']) {
            abort(404);
        }

        return view('compras.nota_remision.show', $datos);
    }

    public function destroy(int $idremisionc)
    {
        try {
            DB::beginTransaction();

            $remision = NotaRemisionCompra::findOrFail($idremisionc);

            if ($this->estaCancelada($remision->estado)) {
                DB::rollBack();
                return Redirect::route('nota_remision_compra.index')
                    ->with('info', 'La nota de remision ya se encuentra cancelada.');
            }

            $remision->estado = 'Cancelado';
            $remision->save();

            DB::commit();

            return Redirect::route('nota_remision_compra.index')
                ->with('success', 'Nota de remision anulada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return Redirect::route('nota_remision_compra.index')
                ->with('error', 'Error al anular la nota de remision.');
        }
    }

    private function obtenerOrden(int $idordencompra): ?object
    {
        return DB::table('orden_compras as oc')
            ->join('proveedores as p', 'oc.idproveedor', '=', 'p.idproveedor')
            ->join('depositos as dep', 'oc.iddeposito', '=', 'dep.iddeposito')
            ->join('sucursales as s', 'oc.idsucursal', '=', 's.idsucursal')
            ->join('users as u', 'oc.idusuario', '=', 'u.id')
            ->select(
                'oc.idordencompra',
                'oc.idsucursal',
                'oc.idproveedor',
                'oc.iddeposito',
                'oc.fecha',
                'oc.estado',
                'u.name as usuario',
                'oc.observacion',
                'oc.monto_orden_compra',
                'p.razonsocial as proveedor',
                'p.ruc',
                'p.direccion',
                'dep.descripcion as deposito',
                's.descripcion as sucursal'
            )
            ->where('oc.idordencompra', '=', $idordencompra)
            ->first();
    }

    private function obtenerDetallesPendientes(int $idordencompra)
    {
        $remitido = DB::table('nota_remision_compra_detalle as nrd')
            ->join('nota_remision_compra as nr', 'nrd.idremisionc', '=', 'nr.idremisionc')
            ->select('nrd.idorden_detalle', DB::raw('SUM(nrd.cantidad) as cantidad_remitida'))
            ->where('nr.idordencompra', '=', $idordencompra)
            ->whereNotIn('nr.estado', ['Cancelado', 'Anulado', 'Anulada', 'A'])
            ->groupBy('nrd.idorden_detalle');

        return DB::table('orden_detalle as od')
            ->join('productos as prod', 'od.idproducto', '=', 'prod.idproducto')
            ->leftJoinSub($remitido, 'rem', function ($join) {
                $join->on('od.idorden_detalle', '=', 'rem.idorden_detalle');
            })
            ->select(
                'od.idorden_detalle',
                'od.idordencompra',
                'od.idproducto',
                'od.items',
                'od.cantidad',
                'od.precio_compra',
                'prod.descripcion as producto',
                DB::raw('(od.cantidad - COALESCE(rem.cantidad_remitida, 0)) as cantidad_pendiente')
            )
            ->where('od.idordencompra', '=', $idordencompra)
            ->whereRaw('(od.cantidad - COALESCE(rem.cantidad_remitida, 0)) > 0')
            ->orderBy('od.items')
            ->get();
    }

    private function datosRemision(int $idremisionc): array
    {
        $remision = DB::table('nota_remision_compra as nr')
            ->join('orden_compras as oc', 'nr.idordencompra', '=', 'oc.idordencompra')
            ->join('proveedores as p', 'nr.idproveedor', '=', 'p.idproveedor')
            ->join('depositos as dep', 'nr.iddeposito', '=', 'dep.iddeposito')
            ->join('sucursales as s', 'nr.idsucursal', '=', 's.idsucursal')
            ->join('users as u', 'nr.idusuario', '=', 'u.id')
            ->select(
                'nr.*',
                'u.name as usuario',
                'oc.fecha as fecha_orden',
                'oc.estado as estado_orden',
                'p.razonsocial as proveedor',
                'p.ruc',
                'p.direccion',
                'dep.descripcion as deposito',
                's.descripcion as sucursal'
            )
            ->where('nr.idremisionc', '=', $idremisionc)
            ->first();

        $detalles = DB::table('nota_remision_compra_detalle as nrd')
            ->join('productos as prod', 'nrd.idproducto', '=', 'prod.idproducto')
            ->join('orden_detalle as od', 'nrd.idorden_detalle', '=', 'od.idorden_detalle')
            ->select(
                'nrd.idremisionc_detalle',
                'nrd.items',
                'nrd.cantidad',
                'prod.descripcion as producto',
                'od.cantidad as cantidad_orden',
                'od.precio_compra'
            )
            ->where('nrd.idremisionc', '=', $idremisionc)
            ->orderBy('nrd.items')
            ->get();

        return compact('remision', 'detalles');
    }

    private function estaCancelada(?string $estado): bool
    {
        return in_array(strtoupper(trim((string) $estado)), ['CANCELADO', 'ANULADO', 'ANULADA', 'A'], true);
    }
}
