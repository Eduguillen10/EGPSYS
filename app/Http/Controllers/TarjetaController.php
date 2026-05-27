<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Tarjeta;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\TarjetaFormRequest;
use DB;

class TarjetaController extends Controller
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

            $tarjetas=DB::table('tarjeta as tar')
            ->join('entidademisora as ent','tar.identidademisora','=','ent.identidademisora')         
            ->select('tar.id_tarjeta','tar.descripcion','tar.estado','ent.identidademisora','ent.descripcion as entidademisora')
            ->where('tar.id_tarjeta','LIKE','%'.$query.'%')
            ->where('tar.descripcion','LIKE','%'.$query2.'%')  
            ->where('ent.descripcion','LIKE','%'.$query3.'%')             
            ->orderBy('tar.id_tarjeta','desc')
            
            ->paginate(7);
            return view('referenciales.tarjetas.index',["tarjetas"=>$tarjetas,"searchText"=>$query,"searchText2"=>$query,"searchText3"=>$query]);
        }
    }

    public function create()
    {
        $entidademisora=DB::table('entidademisora')
        ->where('estado', 'Activo')
        ->select('identidademisora','descripcion')->get();        
        
        return view("referenciales.tarjetas.create",["entidademisora"=>$entidademisora]);
    }
    
    public function store (TarjetaFormRequest $request)
    {
        $tarjeta=new Tarjeta;   
        $tarjeta->identidademisora=$request->get('identidademisora');
        $tarjeta->descripcion=$request->get('descripcion');        
        
        $tarjeta->save();
        return Redirect::to('referenciales/tarjetas');
    }
    
    public function show($id)
    {
        return view("referenciales.tarjetas.show",["tarjeta"=>Tarjeta::findOrFail($id)]);
    }
    
    public function edit($id)
    {
        $tarjetas=Tarjeta::findOrFail($id);
        $entidademisora=DB::table('entidademisora')
        ->where('estado', 'Activo')
        ->select('identidademisora','descripcion')->get();
        
        return view("referenciales.tarjetas.edit",["tarjetas"=>$tarjetas,"entidademisora"=>$entidademisora]);
    }
    
    public function update(TarjetaFormRequest $request, $id)
    {
        $tarjeta=Tarjeta::findOrFail($id);

        $tarjeta->identidademisora=$request->get('identidademisora');
        $tarjeta->descripcion=$request->get('descripcion');  
        
        $tarjeta->save();
        return Redirect::to('referenciales/tarjetas');
    }
    
    public function destroy($id)
    {
        $tarjeta=Tarjeta::findOrFail($id);        
        $tarjeta->estado = 'Inactivo';
        $tarjeta->save();
        return Redirect::to('referenciales/tarjetas');
    }
}
