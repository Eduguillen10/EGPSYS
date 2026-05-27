<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModuloFormRequest;
use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ModuloController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = trim($request->get('searchText'));

        $modulos = Modulo::where('nombre', 'LIKE', '%' . $query . '%')
            ->withCount('ventanas')
            ->orderBy('orden')
            ->orderBy('nombre')
            ->paginate(10);

        return view('acceso.modulos.index', compact('modulos', 'query'));
    }

    public function create()
    {
        return view('acceso.modulos.create');
    }

    public function store(ModuloFormRequest $request)
    {
        Modulo::create([
            'nombre' => $request->get('nombre'),
            'icono' => $request->get('icono') ?: 'fa fa-folder',
            'orden' => $request->get('orden'),
            'estado' => $request->boolean('estado'),
        ]);

        return Redirect::to('acceso/modulos');
    }

    public function edit($id)
    {
        return view('acceso.modulos.edit', [
            'modulo' => Modulo::findOrFail($id),
        ]);
    }

    public function update(ModuloFormRequest $request, $id)
    {
        $modulo = Modulo::findOrFail($id);

        $modulo->update([
            'nombre' => $request->get('nombre'),
            'icono' => $request->get('icono') ?: 'fa fa-folder',
            'orden' => $request->get('orden'),
            'estado' => $request->boolean('estado'),
        ]);

        return Redirect::to('acceso/modulos');
    }

    public function destroy($id)
    {
        $modulo = Modulo::withCount('ventanas')->findOrFail($id);

        if ($modulo->ventanas_count > 0) {
            return Redirect::to('acceso/modulos')
                ->with('error', 'No se puede eliminar un modulo con ventanas asociadas.');
        }

        $modulo->delete();

        return Redirect::to('acceso/modulos');
    }
}
