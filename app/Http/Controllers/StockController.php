<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Stock;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\StockRequest;
use DB;

class StockController extends Controller
{
     public function __construct()
    {
         $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        if ($request)
        {
            $query2 = $request->get('searchText2');
            $query3 = $request->get('searchText3');
            $query4 = $request->get('searchText4');
            $query5 = $request->get('searchText5');

            $stock=DB::table('stock as stk')
            ->join('sucursales as s', 'stk.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'stk.iddeposito', '=', 'dep.iddeposito')
            ->join('productos as prod', 'stk.idproducto', '=', 'prod.idproducto')
            ->select('s.idsucursal','s.descripcion as sucursal','dep.iddeposito','dep.descripcion as deposito', 'prod.idproducto', 'prod.descripcion as producto', 'stk.cantidad')
            ->Where('s.descripcion', 'LIKE', '%'.$query2.'%')
            ->Where('dep.descripcion', 'LIKE', '%'.$query3.'%')
            ->Where('prod.descripcion', 'LIKE', '%'.$query4.'%') 
            ->Where('stk.cantidad', 'LIKE', '%'.$query5.'%') 

            ->orderBy('iddeposito','desc')
            ->paginate(7);
            return view('referenciales.stock.index',["stock"=>$stock,"searchText2"=>$query2,"searchText3"=>$query3,"searchText4"=>$query4,"searchText5"=>$query5]);
        }
    }
}

