<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CuentasPagarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = DB::table('cuentas_a_pagar as cp')
            ->join('compras as c', 'cp.idcompra', '=', 'c.idcompra')
            ->join('proveedores as p', 'cp.idproveedor', '=', 'p.idproveedor')
            ->join('sucursales as s', 'cp.idsucursal', '=', 's.idsucursal')
            ->select(
                'cp.idcuentaapagar',
                'cp.idcompra',
                'cp.montoapagar',
                'cp.montopagado',
                'cp.estado',
                'cp.fecha_factura',
                'cp.fecha_vencimiento',
                's.descripcion as sucursal',
                'p.razonsocial as proveedor',
                'p.ruc',
                'c.nro_factura',
                'c.timbrado',
                'c.estado as estado_compra'
            );

        if ($request->filled('searchText')) {
            $term = trim((string) $request->get('searchText'));
            $query->where(function ($q) use ($term): void {
                $q->where('cp.idcuentaapagar', 'LIKE', '%' . $term . '%')
                    ->orWhere('cp.idcompra', 'LIKE', '%' . $term . '%')
                    ->orWhere('p.razonsocial', 'LIKE', '%' . $term . '%')
                    ->orWhere('p.ruc', 'LIKE', '%' . $term . '%')
                    ->orWhere('c.nro_factura', 'LIKE', '%' . $term . '%');
            });
        }

        if ($request->filled('estado')) {
            $query->where('cp.estado', trim((string) $request->get('estado')));
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('cp.fecha_factura', '>=', $request->get('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('cp.fecha_factura', '<=', $request->get('fecha_hasta'));
        }

        $cuentas = $query
            ->orderByDesc('cp.idcuentaapagar')
            ->paginate(10);

        return view('compras.cuentas_pagar.index', [
            'cuentas' => $cuentas,
            'filtros' => $request->only(['searchText', 'estado', 'fecha_desde', 'fecha_hasta']),
        ]);
    }
}
