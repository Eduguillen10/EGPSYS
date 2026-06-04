<?php

namespace App\Http\Controllers;

use App\Http\Requests\VentanaFormRequest;
use App\Models\Accion;
use App\Models\Modulo;
use App\Models\UsuarioPermiso;
use App\Models\Ventana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class VentanaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = trim($request->get('searchText'));

        $ventanas = Ventana::with('modulo')
            ->where('nombre', 'LIKE', '%' . $query . '%')
            ->orWhere('permiso_clave', 'LIKE', '%' . $query . '%')
            ->orderBy('idmodulo')
            ->orderBy('orden')
            ->paginate(10);

        return view('acceso.ventanas.index', compact('ventanas', 'query'));
    }

    public function create()
    {
        return view('acceso.ventanas.create', [
            'modulos' => Modulo::where('estado', true)->orderBy('orden')->get(),
        ]);
    }

    public function store(VentanaFormRequest $request)
    {
        $ventana = Ventana::create([
            'idmodulo' => $request->get('idmodulo'),
            'nombre' => $request->get('nombre'),
            'ruta' => $request->get('ruta'),
            'permiso_clave' => $request->get('permiso_clave'),
            'orden' => $request->get('orden'),
            'estado' => $request->boolean('estado'),
        ]);

        $this->darPermisosAlUsuarioActual($ventana);

        return Redirect::to('acceso/ventanas');
    }

    public function edit($id)
    {
        return view('acceso.ventanas.edit', [
            'ventana' => Ventana::findOrFail($id),
            'modulos' => Modulo::where('estado', true)->orderBy('orden')->get(),
        ]);
    }

    public function update(VentanaFormRequest $request, $id)
    {
        $ventana = Ventana::findOrFail($id);

        $ventana->update([
            'idmodulo' => $request->get('idmodulo'),
            'nombre' => $request->get('nombre'),
            'ruta' => $request->get('ruta'),
            'permiso_clave' => $request->get('permiso_clave'),
            'orden' => $request->get('orden'),
            'estado' => $request->boolean('estado'),
        ]);

        return Redirect::to('acceso/ventanas');
    }

    public function destroy($id)
    {
        Ventana::findOrFail($id)->delete();

        return Redirect::to('acceso/ventanas');
    }

    private function darPermisosAlUsuarioActual(Ventana $ventana): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $ahora = now();
        $permisos = Accion::all()->map(function ($accion) use ($user, $ventana, $ahora) {
            return [
                'idusuario' => $user->id,
                'idventana' => $ventana->idventana,
                'idaccion' => $accion->idaccion,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ];
        })->toArray();

        UsuarioPermiso::insert($permisos);
    }
}
