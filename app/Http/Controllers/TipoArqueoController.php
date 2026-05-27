<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoArqueo;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\TipoArqueoFormRequest;
use DB;

class TipoArqueoController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        if ($request)
        {
            $query = trim($request->get('searchText'));
            $tipoArqueos = DB::table('tipoarqueo')
                ->where('descripcion', 'LIKE', '%' . $query . '%')            
                ->orderBy('idtipoarqueo', 'desc')
                ->paginate(7);
                
            return view('referenciales.tipo_arqueo.index', ["tipoArqueos" => $tipoArqueos, "searchText" => $query]);
        }
    }    

    public function create()
    {
        return view("referenciales.tipo_arqueo.create");
    }

    public function store(TipoArqueoFormRequest $request)
    {
        $tipoArqueo = new TipoArqueo;
        $tipoArqueo->descripcion = $request->get('descripcion');
        $tipoArqueo->save();
        return Redirect::to('referenciales/tipo_arqueo');
    }

    public function show($id)
    {
        return view("referenciales.tipo_arqueo.show", ["tipoArqueo" => TipoArqueo::findOrFail($id)]);
    }

    public function edit($id)
    {
        return view("referenciales.tipo_arqueo.edit", ["tipoArqueo" => TipoArqueo::findOrFail($id)]);
    }

    public function update(TipoArqueoFormRequest $request, $id)
    {
        $tipoArqueo = TipoArqueo::findOrFail($id);
        $tipoArqueo->update(['descripcion' => $request->get('descripcion')]);
        return Redirect::to('referenciales/tipo_arqueo');
    }

    public function destroy($id)
    {
        $tipoArqueo = TipoArqueo::findOrFail($id);
        $tipoArqueo->estado = 'Inactivo';
        $tipoArqueo->save();
        return Redirect::to('referenciales/tipo_arqueo');
    }
}
