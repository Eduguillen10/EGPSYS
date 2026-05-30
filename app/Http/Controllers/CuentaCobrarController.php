<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;

class CuentaCobrarController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        $filtros = [
            'searchText' => trim((string) $request->get('searchText', '')),
            'estado' => trim((string) $request->get('estado', '')),
            'fecha_desde' => $request->get('fecha_desde'),
            'fecha_hasta' => $request->get('fecha_hasta'),
        ];

        $cuenta_cobrar = DB::table('cuenta_cobrar as cc')
            ->join('ventas as v', 'cc.idventa', '=', 'v.idventa')
            ->leftJoin('sucursales as s', 'v.idsucursal', '=', 's.idsucursal')
            ->join('clientes as cli', 'cc.idcliente', '=', 'cli.idcliente')
            ->select(
                'cc.idcuenta_cobrar',
                'cc.fecha_vencimiento',
                'cc.saldo',
                'cc.importe',
                'v.idsucursal',
                's.descripcion as sucursal',
                'cli.idcliente',
                'cli.nombre as cliente',
                'cli.num_documento',
                'cc.fecha',
                'v.idventa',
                'v.nro_factura',
                'cc.obs',
                'cc.estado',
                'cc.condicion'
            )
            ->when($filtros['searchText'] !== '', function ($query) use ($filtros) {
                $texto = $filtros['searchText'];
                $query->where(function ($subquery) use ($texto) {
                    $subquery->where('cc.idcuenta_cobrar', 'LIKE', "%{$texto}%")
                        ->orWhere('v.idventa', 'LIKE', "%{$texto}%")
                        ->orWhere('v.nro_factura', 'LIKE', "%{$texto}%")
                        ->orWhere('s.descripcion', 'LIKE', "%{$texto}%")
                        ->orWhere('cli.nombre', 'LIKE', "%{$texto}%")
                        ->orWhere('cli.num_documento', 'LIKE', "%{$texto}%");
                });
            })
            ->when($filtros['estado'] !== '', function ($query) use ($filtros) {
                $query->where('cc.estado', $filtros['estado']);
            })
            ->when($filtros['fecha_desde'], function ($query) use ($filtros) {
                $query->whereDate('cc.fecha', '>=', $filtros['fecha_desde']);
            })
            ->when($filtros['fecha_hasta'], function ($query) use ($filtros) {
                $query->whereDate('cc.fecha', '<=', $filtros['fecha_hasta']);
            })
            ->orderBy('cc.idcuenta_cobrar', 'desc')
            ->paginate(10);

        return view('ventas.cuenta_cobrar.index', [
            'cuenta_cobrar' => $cuenta_cobrar,
            'filtros' => $filtros,
        ]);
    }
}
