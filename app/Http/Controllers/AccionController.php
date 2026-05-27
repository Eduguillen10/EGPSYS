<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccionFormRequest;
use App\Models\Accion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AccionController extends Controller
{
    private array $accionesProtegidas = ['ver', 'crear', 'editar', 'anular'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = trim($request->get('searchText'));

        $acciones = Accion::where('nombre', 'LIKE', '%' . $query . '%')
            ->orWhere('clave', 'LIKE', '%' . $query . '%')
            ->orderBy('orden')
            ->paginate(10);

        return view('acceso.acciones.index', compact('acciones', 'query'));
    }

    public function create()
    {
        return view('acceso.acciones.create');
    }

    public function store(AccionFormRequest $request)
    {
        Accion::create([
            'nombre' => $request->get('nombre'),
            'clave' => $request->get('clave'),
            'orden' => $request->get('orden'),
        ]);

        return Redirect::to('acceso/acciones');
    }

    public function edit($id)
    {
        return view('acceso.acciones.edit', [
            'accion' => Accion::findOrFail($id),
        ]);
    }

    public function update(AccionFormRequest $request, $id)
    {
        $accion = Accion::findOrFail($id);

        if (in_array($accion->clave, $this->accionesProtegidas, true) && $accion->clave !== $request->get('clave')) {
            return Redirect::to('acceso/acciones')
                ->with('error', 'No se puede cambiar la clave de una accion protegida.');
        }

        $accion->update([
            'nombre' => $request->get('nombre'),
            'clave' => $request->get('clave'),
            'orden' => $request->get('orden'),
        ]);

        return Redirect::to('acceso/acciones');
    }

    public function destroy($id)
    {
        $accion = Accion::findOrFail($id);

        if (in_array($accion->clave, $this->accionesProtegidas, true)) {
            return Redirect::to('acceso/acciones')
                ->with('error', 'No se puede eliminar una accion protegida.');
        }

        $accion->delete();

        return Redirect::to('acceso/acciones');
    }
}
