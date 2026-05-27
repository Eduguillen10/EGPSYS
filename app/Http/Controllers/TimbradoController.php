<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Timbrado;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\TimbradoFormRequest;
use DB;

class TimbradoController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        if ($request)
        {
            $query = $request->get('searchText');
            $query2 = $request->get('searchText2');
            $query3 = $request->get('searchText3');            
            $query4 = $request->get('searchText4');
            $query5 = $request->get('searchText5');

            $timbrado=DB::table('timbrado as tim')
            ->join('sucursales as s', 'tim.idsucursal', '=', 's.idsucursal')
            ->select('tim.idtimbrado', 'tim.nro_timbrado' ,'tim.nro_inicial','tim.nro_serie','tim.nro_actual','tim.nro_final','tim.fecha_inicial','tim.estado','tim.fecha_vencimiento','s.idsucursal','s.descripcion as sucursal')
            ->where('tim.idtimbrado', 'LIKE', '%'.$query.'%')
            ->Where('s.descripcion', 'LIKE', '%'.$query2.'%')
            ->Where('tim.fecha_inicial', 'LIKE', '%'.$query3.'%')            
            ->Where('tim.fecha_vencimiento', 'LIKE', '%'.$query4.'%')
            ->Where('tim.estado', 'LIKE', '%'.$query5.'%')       
            ->orderBy('idtimbrado','desc')
            ->paginate(7);
            return view('referenciales.timbrado.index',["timbrado"=>$timbrado,"searchText"=>$query,"searchText2"=>$query,"searchText3"=>$query,"searchText4"=>$query,"searchText5"=>$query]);
        }
    }

    public function create()
    {
        $sucursales=DB::table('sucursales as s')
        ->where('s.estado', 'Activo')
        ->select('s.idsucursal','s.descripcion')
        ->get();
        return view("referenciales.timbrado.create",["sucursales"=>$sucursales]);
    }
    
    public function store (TimbradoFormRequest $request)
    {
        $timbrado=new Timbrado;   
        $timbrado->nro_timbrado=$request->get('nro_timbrado');
        $timbrado->nro_inicial=$request->get('nro_inicial'); 
        $timbrado->nro_actual=$request->get('nro_actual');
        $timbrado->nro_final=$request->get('nro_final'); 
        $timbrado->nro_serie=$request->get('nro_serie'); 
        $timbrado->fecha_inicial=$request->get('fecha_inicial');
        $timbrado->estado='Activo'; 
        $timbrado->fecha_vencimiento=$request->get('fecha_vencimiento');
        $timbrado->idsucursal=$request->get('idsucursal');        
        $timbrado->save();
        return Redirect::to('referenciales/timbrado');
    }
    
    public function show($id)
    {
        return view("referenciales.timbrado.show",["timbrado"=>Timbrado::findOrFail($id)]);
    }
    
    public function edit($id)
    {
        $timbrado=Timbrado::findOrFail($id);
        $sucursales=DB::table('sucursales as s')
        ->where('s.estado', 'Activo')
        ->select('s.idsucursal','s.descripcion')
        ->get();
        return view("referenciales.timbrado.edit",["timbrado"=>$timbrado,"sucursales"=>$sucursales]);
    }
    
    public function update(TimbradoFormRequest $request, $id)
    {
        $timbrado=Timbrado::findOrFail($id);
        $timbrado->nro_timbrado=$request->get('nro_timbrado');
        $timbrado->nro_inicial=$request->get('nro_inicial'); 
        $timbrado->nro_actual=$request->get('nro_actual');
        $timbrado->nro_final=$request->get('nro_final');
        $timbrado->nro_serie=$request->get('nro_serie'); 
        $timbrado->fecha_inicial=$request->get('fecha_inicial');
        $timbrado->estado=$request->get('estado'); 
        $timbrado->fecha_vencimiento=$request->get('fecha_vencimiento');
        $timbrado->idsucursal=$request->get('idsucursal');
        $timbrado->update();
        return Redirect::to('referenciales/timbrado');
    }
    
    public function destroy($id)
    {
        try {
            $timbrado = Timbrado::findOrFail($id);
            $timbrado->estado = 'Inactivo';
            $timbrado->save();
    
            return Redirect::to('referenciales/timbrado')->with('success', 'Timbrado Inactivado correctamente.');
        } catch (Exception $e) {
            return Redirect::to('referenciales/timbrado')->with('error', 'Error al inactivar el timbrado.');
        }
    }
}
