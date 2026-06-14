<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;

class LibroVentasController extends Controller
{
    public function index(Request $request)
    {
        $fechaDesde = $request->get('fecha_desde', now()->startOfMonth()->toDateString());
        $fechaHasta = $request->get('fecha_hasta', now()->toDateString());
        $datos = $this->obtenerDatosLibro($fechaDesde, $fechaHasta);

        return view('ventas.libro_ventas.index', array_merge($datos, [
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
        ]));
    }

    public function generado(Request $request)
    {
        $fecha_desde = $request->get('fecha_desde', now()->startOfMonth()->toDateString());
        $fecha_hasta = $request->get('fecha_hasta', now()->toDateString());
        $datos = $this->obtenerDatosLibro($fecha_desde, $fecha_hasta);

        return view('ventas.libro_ventas.generado', array_merge($datos, [
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
        ]));
    }

    private function obtenerDatosLibro(string $fechaDesde, string $fechaHasta): array
    {
        $libroventas = DB::table('ventas as v')
        ->join('clientes as c', 'v.idcliente', '=', 'c.idcliente')
        ->join('timbrado as tim', 'v.idtimbrado', '=', 'tim.idtimbrado')
        ->select('v.idventa','c.idcliente','c.nombre as cliente','c.num_documento','v.montoiva10', 'v.montoiva5', 'v.montogravada10', 'v.montogravada5', 'v.montoexenta', 'v.montoventa', 'tim.idtimbrado','tim.nro_timbrado as timbrado', 'v.nro_factura','v.fecha')
        ->whereDate('v.fecha', '>=' , $fechaDesde)
        ->whereDate('v.fecha', '<=' , $fechaHasta)
        ->whereNotIn('v.estado', ['Anulado', 'Anulada', 'A', 'Cancelado', 'Cancelada'])
        ->orderBy('fecha','asc')
        ->get();

        $nota_creditov = DB::table('nota_credito_venta as v')
        ->join('sucursales as s', 'v.idsucursal', '=', 's.idsucursal')
        ->join('depositos as dep', 'v.iddeposito', '=', 'dep.iddeposito')
        ->join('clientes as c', 'v.idcliente', '=', 'c.idcliente')
        ->join('users as u', 'v.idusuario', '=', 'u.id')
        ->select('v.idnota_creditov','v.idventa', 'u.name as usuario', 's.idsucursal', 's.descripcion as sucursal', 'dep.iddeposito', 'dep.descripcion as deposito', 'c.idcliente', 'c.nombre as cliente', 'c.num_documento', 'v.fecha_registro', 'v.totaliva10', 'v.totaliva5', 'v.totalgravada10', 'v.totalgravada5', 'v.totalexenta', 'v.totalventa', 'v.condicion', 'v.concepto' , 'v.nro_factura','v.estado','v.fecha_factura','v.fecha_vencimiento')
        ->whereDate('v.fecha_factura', '>=' , $fechaDesde)
        ->whereDate('v.fecha_factura', '<=' , $fechaHasta)
        ->whereNotIn('v.estado', ['Anulado', 'Anulada', 'A', 'Cancelado', 'Cancelada'])
        ->orderBy('v.idnota_creditov', 'asc')
        ->get();

        $nota_debitov = DB::table('nota_debito_venta as v')
        ->join('sucursales as s', 'v.idsucursal', '=', 's.idsucursal')
        ->join('depositos as dep', 'v.iddeposito', '=', 'dep.iddeposito')
        ->join('clientes as c', 'v.idcliente', '=', 'c.idcliente')
        ->join('users as u', 'v.idusuario', '=', 'u.id')
        ->select('v.idnota_debitov','v.idventa', 'u.name as usuario', 's.idsucursal', 's.descripcion as sucursal', 'dep.iddeposito', 'dep.descripcion as deposito', 'c.idcliente', 'c.nombre as cliente', 'c.num_documento', 'v.fecha_registro', 'v.totaliva10', 'v.totaliva5', 'v.totalgravada10', 'v.totalgravada5', 'v.totalexenta', 'v.totalventa', 'v.condicion', 'v.concepto' , 'v.nro_factura','v.estado','v.fecha_factura','v.fecha_vencimiento')
        ->whereDate('v.fecha_factura', '>=' , $fechaDesde)
        ->whereDate('v.fecha_factura', '<=' , $fechaHasta)
        ->whereNotIn('v.estado', ['Anulado', 'Anulada', 'A', 'Cancelado', 'Cancelada'])
        ->orderBy('v.idnota_debitov', 'asc')
        ->get();

        return [
            'libroventas' => $libroventas,
            'nota_creditov' => $nota_creditov,
            'nota_debitov' => $nota_debitov,
        ];
    }
}
