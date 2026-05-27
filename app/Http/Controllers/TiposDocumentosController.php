<?php

namespace App\Http\Controllers;

use App\Http\Requests\TiposDocumentosFormRequest;
use App\Models\TiposDocumentos;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use DB;

class TiposDocumentosController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        if ($request) {
            $query = trim($request->get('searchText'));
            $tiposDocumentos = DB::table('tipos_documentos')
                ->where('descripcion', 'LIKE', '%' . $query . '%')
                ->orderBy('idtipodocumento', 'desc')
                ->paginate(7);

            return view('referenciales.tipos_documentos.index', ["tiposDocumentos" => $tiposDocumentos, "searchText" => $query]);
        }
    }

    public function create()
    {
        return view('referenciales.tipos_documentos.create');
    }

    public function store(TiposDocumentosFormRequest $request)
    {
        $tiposDocumentos = new TiposDocumentos;
        $tiposDocumentos->descripcion = $request->get('descripcion');
        $tiposDocumentos->save();

        return Redirect::to('referenciales/tipos_documentos');
    }

    public function show($id)
    {
        $tiposDocumentos = TiposDocumentos::findOrFail($id);
        return view('referenciales.tipos_documentos.show', ['tiposDocumentos' => $tiposDocumentos]);
    }

    public function edit($id)
    {
        $tiposDocumentos = TiposDocumentos::findOrFail($id);
        return view('referenciales.tipos_documentos.edit', ['tiposDocumentos' => $tiposDocumentos]);
    }

    public function update(TiposDocumentosFormRequest $request, $id)
    {
        $tiposDocumentos = TiposDocumentos::findOrFail($id);
        $tiposDocumentos->update(['descripcion' => $request->get('descripcion')]);

        return Redirect::to('referenciales/tipos_documentos');
    }

    public function destroy($id)
    {
        $tiposDocumentos = TiposDocumentos::findOrFail($id);
        $tiposDocumentos->estado = 'Inactivo';
        $tiposDocumentos->save();

        return Redirect::to('referenciales/tipos_documentos');
    }
}
