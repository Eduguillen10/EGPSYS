<?php

namespace App\Http\Controllers;

use App\Http\Requests\CargosFormRequest;
use App\Models\Cargos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use DB;

class CargosController extends Controller
{
    public function __construct()
    {

    }
    
    public function index(Request $request)
    {
        if ($request) {
            $query = trim($request->get('searchText'));
            $cargos = DB::table('cargos')
                ->where('descripcion', 'LIKE', '%' . $query . '%')
                ->orderBy('idcargo', 'desc')
                ->paginate(10);

            return view('referenciales.cargos.index', ["cargos" => $cargos, "searchText" => $query]);
        }
    }

    public function create()
    {
        return view("referenciales.cargos.create");
    }

    public function store(CargosFormRequest $request)
    {
        $cargo = new Cargos;
        $cargo->descripcion = $request->get('descripcion');
        $cargo->save();
        return Redirect::to('referenciales/cargos');
    }

    public function show($id)
    {
        return view("referenciales.cargos.show",["cargos"=>Cargos::findOrFail($id)]);
    }

    public function edit($id)
    {
        $cargo = Cargos::findOrFail($id);
        return view("referenciales.cargos.edit", ["cargo" => $cargo]);
    }

    public function update(CargosFormRequest $request, $id)
    {
        $cargo = Cargos::findOrFail($id);
        $cargo->descripcion = $request->get('descripcion');
        $cargo->update();
        return Redirect::to('referenciales/cargos');
    }

    public function destroy($id)
    {
        $cargo = Cargos::findOrFail($id);
        $cargo->estado = 'Inactivo';
        $cargo->save();
        return Redirect::to('referenciales/cargos');
    }
}
