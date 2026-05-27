<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresas;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\EmpresasFormRequest;
use DB;

class EmpresasController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        if ($request)
        {
            $query = trim($request->get('searchText'));
            $empresas = DB::table('empresas')
                ->where('descripcion', 'LIKE', '%' . $query . '%')            
                ->orderBy('idempresa', 'desc')
                ->paginate(7);

            return view('referenciales.empresas.index', ["empresas" => $empresas, "searchText" => $query]);
        }
    }

    public function create()
    {
        return view("referenciales.empresas.create");
    }

    public function store(EmpresasFormRequest $request)
    {
        $empresa = new Empresas;
        $empresa->descripcion = $request->get('descripcion');
        $empresa->save();
        return Redirect::to('referenciales/empresas');
    }

    public function show($id)
    {
        return view("referenciales.empresas.show", ["empresa" => Empresas::findOrFail($id)]);
    }

    public function edit($id)
    {
        return view("referenciales.empresas.edit", ["empresa" => Empresas::findOrFail($id)]);
    }

    public function update(EmpresasFormRequest $request, $id)
    {
        $empresa = Empresas::findOrFail($id);
        $empresa->update(['descripcion' => $request->get('descripcion')]);
        return Redirect::to('referenciales/empresas');
    }

    public function destroy($id)
    {
        $empresa = Empresas::findOrFail($id);
        $empresa->estado = 'Inactivo';
        $empresa->save();
        return Redirect::to('referenciales/empresas');
    }
}
