<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sucursales;
use App\Models\Empresas;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\SucursalesFormRequest;
use DB;

class SucursalesController extends Controller
{
    public function index(Request $request)
    {
        if ($request)
        {
            $query = trim($request->get('searchText'));
            $sucursales = DB::table('sucursales')
                ->join('empresas', 'sucursales.idempresa', '=', 'empresas.idempresa')
                ->select('sucursales.*', 'empresas.descripcion as empresa_descripcion')
                ->where('sucursales.descripcion', 'LIKE', '%' . $query . '%')            
                ->orderBy('sucursales.idsucursal', 'desc')
                ->paginate(7);

            return view('referenciales.sucursales.index', ["sucursales" => $sucursales, "searchText" => $query]);
        }
    }
public function create()
{
    $empresas = Empresas::where('estado', 'Activo')->get();
    return view("referenciales.sucursales.create", compact('empresas'));
}

public function store(SucursalesFormRequest $request)
{
    $sucursal = new Sucursales;
    $sucursal->idempresa = $request->get('idempresa');
    $sucursal->descripcion = $request->get('descripcion');
    $sucursal->save();

    return Redirect::to('referenciales/sucursales');
}

public function edit($id)
{
    $sucursal = Sucursales::findOrFail($id);
    $empresas = Empresas::where('estado', 'Activo')->get();

    return view("referenciales.sucursales.edit", compact('sucursal', 'empresas'));
}

public function update(SucursalesFormRequest $request, $id)
{
    $sucursal = Sucursales::findOrFail($id);
    $sucursal->idempresa = $request->get('idempresa');
    $sucursal->descripcion = $request->get('descripcion');
    $sucursal->update();

    return Redirect::to('referenciales/sucursales');
}

public function destroy($id)
{
    $sucursal = Sucursales::findOrFail($id);
    $sucursal->estado = 'Inactivo';
    $sucursal->save();

    return Redirect::to('referenciales/sucursales');
}

}
