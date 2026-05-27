<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Depositos;
use App\Models\Sucursales;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\DepositosFormRequest;
use DB;

class DepositosController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        if ($request)
        {
            $query = trim($request->get('searchText'));
            $depositos = DB::table('depositos')
            ->join('sucursales', 'depositos.idsucursal', '=', 'sucursales.idsucursal')
            ->select('depositos.*', 'sucursales.descripcion as sucursal') 
            ->where('depositos.descripcion', 'LIKE', '%' . $query . '%')           
                ->orderBy('iddeposito', 'desc')
                ->paginate(7);

            return view('referenciales.depositos.index', ["depositos" => $depositos, "searchText" => $query]);
        }
    }

    public function create()
    {
        $sucursales = Sucursales::where('estado', 'Activo')->get();
        return view("referenciales.depositos.create", compact('sucursales'));
    }

    public function store(DepositosFormRequest $request)
    {
        $depositos = new Depositos;
        $depositos->idsucursal = $request->get('idsucursal');
        $depositos->descripcion = $request->get('descripcion');
        $depositos->save();
        return Redirect::to('referenciales/depositos');
    }

    public function show($id)
    {
        return view("referenciales.depositos.show", ["deposito" => Depositos::findOrFail($id)]);
    }

    public function edit($id)
    {
        $depositos = Depositos::findOrFail($id);
        $sucursales = Sucursales::where('estado', 'Activo')->get();
        return view("referenciales.depositos.edit", ["deposito" => $depositos, "sucursales" => $sucursales]);
    }

    public function update(DepositosFormRequest $request, $id)
    {
        $depositos = Depositos::findOrFail($id);
        $depositos->idsucursal = $request->get('idsucursal');
        $depositos->update(['descripcion' => $request->get('descripcion')]);
        return Redirect::to('referenciales/depositos');
    }

    public function destroy($id)
    {
        $depositos = Depositos::findOrFail($id);
        $depositos->estado = 'Inactivo';
        $depositos->save();
        return Redirect::to('referenciales/depositos');
    }
}
