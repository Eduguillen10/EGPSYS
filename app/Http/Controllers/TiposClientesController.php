<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TiposClientesFormRequest;
use App\Models\TiposClientes;
use Illuminate\Support\Facades\Redirect;
use DB;

class TiposClientesController extends Controller
{
    public function index(Request $request)
    {
        if ($request) {
            $query = trim($request->get('searchText'));
            $tiposClientes = DB::table('tipo_cliente')
                ->where('descripcion', 'LIKE', '%' . $query . '%')
                ->orderBy('idtipo_cliente', 'desc')
                ->paginate(7);

            return view('referenciales.tipos_clientes.index', ["tiposClientes" => $tiposClientes, "searchText" => $query]);
        }
    }

    public function create()
    {
        return view('referenciales.tipos_clientes.create');
    }

    public function store(TiposClientesFormRequest $request)
    {
        $tiposClientes = new TiposClientes;
        $tiposClientes->descripcion = $request->get('descripcion');
        $tiposClientes->save();

        return Redirect::to('referenciales/tipos_clientes');
    }

    public function show($id)
    {
        $tiposClientes = TiposClientes::findOrFail($id);
        return view('referenciales.tipos_clientes.show', ['tiposClientes' => $tiposClientes]);
    }

    public function edit($id)
    {
        $tiposClientes = TiposClientes::findOrFail($id);
        return view('referenciales.tipos_clientes.edit', ['tiposClientes' => $tiposClientes]);
    }

    public function update(TiposClientesFormRequest $request, $id)
    {
        $tiposClientes = TiposClientes::findOrFail($id);
        $tiposClientes->update(['descripcion' => $request->get('descripcion')]);

        return Redirect::to('referenciales/tipos_clientes');
    }

    public function destroy($id)
    {
        $tiposClientes = TiposClientes::findOrFail($id);
        $tiposClientes->estado = 'Inactivo';
        $tiposClientes->save();

        return Redirect::to('referenciales/tipos_clientes');
    }
}
