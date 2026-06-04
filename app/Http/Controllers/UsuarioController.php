<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\User;
use App\Models\Accion;
use App\Models\Empresas;
use App\Models\Modulo;
use App\Models\Sucursales;
use App\Models\UsuarioPermiso;
use App\Models\UsuarioSucursal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\UsuarioFormRequest;
use DB;

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request) {
            $query=trim($request->get('searchText'));
            $usuarios=DB::table('users')->where('name','LIKE','%'.$query.'%')
            ->orderBy('id','desc')
            ->paginate(7);
            return view('acceso.usuario.index',["usuarios"=>$usuarios,"searchText"=>$query]);
        }
    }

    public function create()
    {
        return view("acceso.usuario.create", $this->datosFormularioPermisos());
    }

    public function store (UsuarioFormRequest $request)
    {
        DB::beginTransaction();

        try {
            $usuario=new User;
            $usuario->name=$request->get('name');
            $usuario->email=$request->get('email');
            $usuario->password=bcrypt($request->get('password'));
            $usuario->save();

            $this->sincronizarPermisos($usuario, $request->input('permisos', []));

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        return Redirect::to('acceso/usuario');
    }

    public function edit($id)
    {
        $usuario = User::findOrFail($id);

        return view("acceso.usuario.edit", array_merge(
            ["usuario" => $usuario],
            $this->datosFormularioPermisos($usuario)
        ));
    }

    public function update(UsuarioFormRequest $request,$id)
    {
        DB::beginTransaction();

        try {
            $usuario=User::findOrFail($id);
            $usuario->name=$request->get('name');
            $usuario->email=$request->get('email');
            $usuario->password=bcrypt($request->get('password'));
            $usuario->save();

            $this->sincronizarPermisos($usuario, $request->input('permisos', []));

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        return Redirect::to('acceso/usuario');
    }   
    
    public function destroy($id)
    {
        $usuario = DB::table('users')->where('id','=', $id)->delete();
        return Redirect::to('acceso/usuario');
    }

    public function verSeleccionarEmpresaSucursal()
{
    // Aca se obtiene las empresas y sucursales disponibles desde la base de datos
    $empresas = Empresas::all();
    $sucursales = Sucursales::all();

    return view('seleccionsucursales.seleccionar.index', compact('empresas', 'sucursales'));

}

    public function seleccionarEmpresaSucursal(Request $request)
    {
        $idempresa = $request->input('empresa');
        $idsucursal = $request->input('sucursal');
        $idusuario = $request->input('idusuario');

        $usuario = User::find($idusuario);

        if ($idsucursal) {
            UsuarioSucursal::updateOrCreate([
                'idusuario' => $idusuario,
                'idsucursal' => $idsucursal,
                'idempresa' => $idempresa,
            ], [
                'idusuario' => $idusuario,
                'idempresa' => $idempresa,
                'idsucursal' => $idsucursal,
            ]);

            // Redirige a la página principal o a donde sea necesario
            return redirect('/home')->with('success', 'Empresa y sucursal seleccionadas correctamente');
        } else {
            // Manejo de error: idsucursal no está presente
            return redirect('/seleccionar-empresa-sucursal')->with('error', 'Error al seleccionar sucursal');
        }
    }

    public function getSucursalesPorEmpresa($idempresa)
    {
        $sucursales = Sucursal::where('idempresa', $idempresa)->get();
        return response()->json($sucursales);
    }

    private function datosFormularioPermisos(?User $usuario = null): array
    {
        $modulos = Modulo::with(['ventanas' => function ($query) {
            $query->where('estado', true)->orderBy('orden');
        }])
            ->where('estado', true)
            ->orderBy('orden')
            ->get();

        $acciones = Accion::orderBy('orden')->get();
        $permisosAsignados = [];

        if ($usuario) {
            foreach ($usuario->permisos as $permiso) {
                $permisosAsignados[$permiso->idventana][] = $permiso->idaccion;
            }
        }

        return compact('modulos', 'acciones', 'permisosAsignados');
    }

    private function sincronizarPermisos(User $usuario, array $permisos): void
    {
        UsuarioPermiso::where('idusuario', $usuario->id)->delete();

        $registros = [];
        $ahora = now();

        foreach ($permisos as $idventana => $acciones) {
            foreach (array_unique((array) $acciones) as $idaccion) {
                $registros[] = [
                    'idusuario' => $usuario->id,
                    'idventana' => (int) $idventana,
                    'idaccion' => (int) $idaccion,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ];
            }
        }

        foreach (array_chunk($registros, 500) as $chunk) {
            UsuarioPermiso::insert($chunk);
        }
    }



}
