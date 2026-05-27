<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EntidadEmisora;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\EntidadEmisoraFormRequest;
use DB;

class EntidadEmisoraController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        if ($request)
        {
            $query = trim($request->get('searchText'));
            $entidademisoras = DB::table('entidademisora')
                ->where('descripcion', 'LIKE', '%' . $query . '%')            
                ->orderBy('identidademisora', 'desc')
                ->paginate(7);
            
            return view('referenciales.entidademisora.index', ["entidademisoras" => $entidademisoras, "searchText" => $query]);
        }
    }

    public function create()
    {
        return view("referenciales.entidademisora.create");
    }

    public function store(EntidadEmisoraFormRequest $request)
    {
        $entidademisora = new EntidadEmisora;
        $entidademisora->descripcion = $request->get('descripcion');
        $entidademisora->save();
        return Redirect::to('referenciales/entidademisora');
    }

    public function show($id)
    {
        return view("referenciales.entidademisora.show", ["entidademisora" => EntidadEmisora::findOrFail($id)]);
    }

    public function edit($id)
    {
        return view("referenciales.entidademisora.edit", ["entidademisora" => EntidadEmisora::findOrFail($id)]);
    }

    public function update(EntidadEmisoraFormRequest $request, $id)
    {
        $entidademisora = EntidadEmisora::findOrFail($id);
        $entidademisora->update(['descripcion' => $request->get('descripcion')]);
        return Redirect::to('referenciales/entidademisora');
    }

    public function destroy($id)
    {
        $entidademisora = EntidadEmisora::findOrFail($id);
        $entidademisora->estado = 'Inactivo';
        $entidademisora->save();
        return Redirect::to('referenciales/entidademisora');
    }
}
