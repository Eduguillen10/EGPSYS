<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Stock;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\StockRequest;
use DB;

class CuentaCobrarController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        if ($request)
        {
            $query=trim($request->get('searchText'));
            $query2=trim($request->get('searchText2'));
            $query3=trim($request->get('searchText3'));
            $query4=trim($request->get('searchText4'));

            $cuenta_cobrar=DB::table('cuenta_cobrar as cc')
            ->join('ventas as v', 'cc.idventa', '=', 'v.idventa')
            ->leftJoin('sucursales as s', 'v.idsucursal', '=', 's.idsucursal')
            ->join('clientes as cli', 'cc.idcliente', '=', 'cli.idcliente')            
            ->select('cc.idcuenta_cobrar', 'cc.fecha_vencimiento','cc.saldo','cc.importe','v.idsucursal','s.descripcion as sucursal', 'cli.idcliente', 'cli.nombre as cliente','cc.fecha','v.idventa', 'v.nro_factura','cc.obs','cc.estado','cc.condicion')

            ->where('cc.idcuenta_cobrar','LIKE','%'.$query.'%')  
            ->where('v.idventa','LIKE','%'.$query2.'%')  
            ->where('s.descripcion','LIKE','%'.$query3.'%')  
            ->where('cli.nombre','LIKE','%'.$query4.'%')  

            ->orderBy('cc.idventa','desc')
            ->paginate(7);
            return view('ventas.cuenta_cobrar.index',["cuenta_cobrar"=>$cuenta_cobrar,"searchText"=>$query,"searchText2"=>$query2,"searchText3"=>$query3,"searchText4"=>$query4]);
        }
    }
          
    
}