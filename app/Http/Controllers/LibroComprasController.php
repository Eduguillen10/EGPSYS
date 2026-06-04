<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LibroComprasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $fechaDesde = $request->get('fecha_desde', now()->startOfMonth()->toDateString());
        $fechaHasta = $request->get('fecha_hasta', now()->toDateString());
        $libroCompras = $this->obtenerLibroCompras($fechaDesde, $fechaHasta);

        return view('compras.libro_compras.index', [
            'libroCompras' => $libroCompras,
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
        ]);
    }

    public function reporte(Request $request)
    {
        $fechaDesde = $request->get('fecha_desde', now()->startOfMonth()->toDateString());
        $fechaHasta = $request->get('fecha_hasta', now()->toDateString());
        $libroCompras = $this->obtenerLibroCompras($fechaDesde, $fechaHasta);

        return view('compras.libro_compras.reporte', [
            'libroCompras' => $libroCompras,
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
        ]);
    }

    private function obtenerLibroCompras(string $fechaDesde, string $fechaHasta)
    {
        return DB::table('libro_compras as lc')
            ->join('compras as c', 'lc.idcompra', '=', 'c.idcompra')
            ->join('proveedores as p', 'lc.idproveedor', '=', 'p.idproveedor')
            ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
            ->select(
                'lc.idlibrocompra',
                'lc.idcompra',
                'lc.idproveedor',
                'lc.monto',
                'lc.montoexenta',
                'lc.montoiva5',
                'lc.montoiva10',
                'lc.montogravada5',
                'lc.montogravada10',
                'lc.estado as estado_libro',
                'c.fecha_factura',
                'c.nro_factura',
                'c.timbrado',
                'c.estado',
                'p.razonsocial as proveedor',
                'p.ruc',
                's.descripcion as sucursal'
            )
            ->whereDate('c.fecha_factura', '>=', $fechaDesde)
            ->whereDate('c.fecha_factura', '<=', $fechaHasta)
            ->orderBy('c.fecha_factura')
            ->orderBy('c.idcompra')
            ->get();
    }
}
