<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cajas;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\CajasFormRequest;
use Illuminate\Support\Facades\Auth;
use DB;

class CajasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request)
        {
            $query = trim($request->get('searchText'));
            $cajas = DB::table('cajas as c')
                ->join('users as u', 'c.id', '=', 'u.id')
                ->select('c.idcaja','c.descripcion','c.estado','c.id','u.name as usuario')
                ->where('c.descripcion', 'LIKE', '%' . $query . '%')
                ->where('c.estado', '=', 'Activo')
                ->orWhere('c.estado','=', 'Inactivo')
                ->orderBy('c.idcaja', 'desc')
                ->paginate(7);
            return view('referenciales.cajas.index', ["cajas" => $cajas, "searchText" => $query]);
        }
    }

    public function create()
    {
        return view("referenciales.cajas.create");
    }

    public function store(CajasFormRequest $request)
    {
        $caja = new Cajas;
        $caja->descripcion = $request->get('descripcion');
        $caja->id = Auth::check() ? Auth::id() : null;  // Obtiene el ID del usuario autenticado // Verifica si hay usuario autenticado
        $caja->estado = 'Activo'; // Estado por defecto
        $caja->save();
        
        return Redirect::to('referenciales/cajas')->with('success', 'Caja creada correctamente.');
    }

    public function show($id)
    {
        return view("referenciales.cajas.show", ["cajas" => Cajas::findOrFail($id)]);
    }

    public function edit($id)
    {
        return view("referenciales.cajas.edit", ["cajas" => Cajas::findOrFail($id)]);
    }

    public function update(CajasFormRequest $request, $id)
    {
        $caja = Cajas::findOrFail($id);
        $caja->descripcion = $request->get('descripcion');
        $caja->estado = $request->get('estado');
        $caja->save();

    return Redirect::to('referenciales/cajas')->with('success', 'Caja actualizada correctamente.');
    }

    public function destroy($id)
    {
        $caja = Cajas::findOrFail($id);
        $caja->update(['estado' => 'Inactivo']);
        return Redirect::to('referenciales/cajas')->with('success', 'Caja desactivada correctamente.');
    }
}

