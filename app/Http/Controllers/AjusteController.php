<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\AjusteRequest;
use App\Models\Ajuste;
use App\Models\AjusteDetalle;
use DB;

use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;

class AjusteController extends Controller
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

           $ajustes=DB::table('ajustes_productos as a')
           ->join('depositos as d','a.iddeposito','=','d.iddeposito') 
           ->join('sucursales as s','a.idsucursal','=','s.idsucursal') 
           ->join('motivo as m','a.idmotivo','=','m.idmotivo')           
           ->select('a.idajusteproducto','m.idmotivo', 'm.descripcion as motivo' ,'a.fecha','a.tipoajuste','s.idsucursal','s.descripcion as sucursal' ,'d.iddeposito','d.descripcion as deposito','usuario' )
           ->where('a.idajusteproducto','LIKE','%'.$query.'%')
           ->orderBy('a.idajusteproducto','desc')
           
           ->paginate(7);
           return view('compras.ajuste.index',["ajustes"=>$ajustes,"searchText"=>$query]);
        }
    }

    public function create()
    {
        $suc = Auth::user()->trabaja_sucursal;

        $sucursales=DB::table('sucursales as s')
        ->select('s.idsucursal','s.descripcion')
        ->where('idsucursal', '=', $suc)
        ->first();
        $depositos=DB::table('depositos as dep')
        ->select('dep.iddeposito','dep.descripcion')
        ->get();        
        $productos = DB::table('productos as prov')
        ->select(DB::raw('CONCAT(prov.codigo, " " ,prov.descripcion) AS producto'),'prov.idproducto')
        ->where('prov.estado','=','Activo')
        ->get();        

        $motivo_ajuste=DB::table('motivo')->get();
        
            // Obtener el último ID de ajuste de producto y sumarle 1
    $ultimoAjuste = DB::table('ajustes_productos')->orderBy('idajusteproducto', 'desc')->first();
    $idajusteproducto = ($ultimoAjuste) ? $ultimoAjuste->idajusteproducto + 1 : 1;

    return view("compras.ajuste.create", [
        "depositos" => $depositos,
        "productos" => $productos,
        "sucursales" => $sucursales,
        "motivo_ajuste" => $motivo_ajuste,
        "idajusteproducto" => $idajusteproducto //Pasar la variable a la vista
    ]);
    }
}
