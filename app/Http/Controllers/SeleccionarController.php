<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use DB;

class SeleccionarController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth');
    }
    public function index(Request $request)
    {
    	
    	$iduser=Auth::user()->id;

    	if ($request)
    	{
    		$query=trim ($request->get('searchText'));
            $sucursales=DB::table('sucursales')
            ->Where('descripcion','LIKE','%'.$query.'%')
    		->orderBy('idsucursal','asc')
    		->paginate(10);
    		return view('seleccionsucursales.seleccionar.index', ["sucursales"=>$sucursales, "searchText"=>$query]);
    	}

    }
    public function edit($id)
    {
     	$iduser=Auth::user()->id;

        DB::update('update users Set trabaja_sucursal='.$id.' where id='.$iduser);
    	
    	return Redirect::to('/home');	
    }
}
