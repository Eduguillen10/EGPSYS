<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marcas;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\MarcasFormRequest;
use DB;

class MarcasController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        if ($request)
        {
            $query=trim($request->get('searchText'));
            $marcas=DB::table('marcas')
            ->where('descripcion','LIKE','%'.$query.'%')            
            ->orderBy('idmarca','desc')
            ->paginate(7);
            return view('referenciales.marcas.index',["marcas"=>$marcas,"searchText"=>$query]);
        }
    }

    public function create()
    {
        return view("referenciales.marcas.create");
    }

    public function store(MarcasFormRequest $request)
    {
        $marcas=new Marcas;
        $marcas->descripcion=$request->get('descripcion');
        $marcas->save();
        return Redirect::to('referenciales/marcas');
    }

    public function show($id)
    {
        return view("referenciales.marcas.show",["marcas"=>Marcas::findOrFail($id)]);
    }

    public function edit($id)
    {
        return view("referenciales.marcas.edit",["marca"=>Marcas::findOrFail($id)]);
    }

    public function update(MarcasFormRequest $request, $id)
    {
    $marcas = Marcas::findOrFail($id);
    $marcas->update(['descripcion' => $request->get('descripcion')]);
    return Redirect::to('referenciales/marcas');
    }

    public function destroy($id)
    {
        $marcas=Marcas::findOrFail($id);  // Corrección aquí
        $marcas->estado = 'Inactivo';
        $marcas->save();
        return Redirect::to('referenciales/marcas');
    }
}
