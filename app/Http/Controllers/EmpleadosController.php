<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmpleadosFormRequest;
use App\Models\Cargos;
use App\Models\Ciudades;
use App\Models\Empleados;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use DB;

class EmpleadosController extends Controller
{
    public function index(Request $request)
    {
        if ($request) {
            $query = trim($request->get('searchText'));
            $empleados = DB::table('empleados')
                ->join('ciudades', 'empleados.idciudad', '=', 'ciudades.idciudad')
                ->join('cargos', 'empleados.idcargo', '=', 'cargos.idcargo')
                ->select('empleados.*', 'ciudades.descripcion as ciudad', 'cargos.descripcion as cargo')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orWhere('apellido', 'LIKE', '%' . $query . '%')
                ->orderBy('idempleado', 'desc')
                ->paginate(7);
    
            return view('referenciales.empleados.index', ["empleados" => $empleados, "searchText" => $query]);
        }
    }

    public function create()
    {
        $ciudades = Ciudades::all();
        $cargos = Cargos::all();
        return view("referenciales.empleados.create", ["ciudades" => $ciudades, "cargos" => $cargos]);
    }

    public function store(EmpleadosFormRequest $request)
    {
        // Validar la unicidad del num_documento
        $existingempleado = Empleados::where('ci', $request->ci)->first();
        if ($existingempleado) {
            return redirect()->back()->withInput()->withErrors(['ci' => 'El número de C.I. ya está en uso.']);
        }   
        $empleado = new Empleados;
        $empleado->idciudad = $request->get('idciudad');
        $empleado->idcargo = $request->get('idcargo');
        $empleado->nombre = $request->get('nombre');
        $empleado->apellido = $request->get('apellido');
        $empleado->ci = $request->get('ci');
        $empleado->direccion = $request->get('direccion');
        $empleado->telefono = $request->get('telefono');
        $empleado->estado = 'Activo';
        $empleado->save();
        return Redirect::to('referenciales/empleados');
    }

    public function show($id)
    {
        return view("referenciales.empleados.show", ["empleado" => Empleados::findOrFail($id)]);
    }

    public function edit($id)
    {
        $empleado = Empleados::findOrFail($id);
        $ciudades = Ciudades::all();
        $cargos = Cargos::all();
        return view("referenciales.empleados.edit", ["empleado" => $empleado, "ciudades" => $ciudades, "cargos" => $cargos]);
    }

    public function update(EmpleadosFormRequest $request, $id)
    {
        $empleado = Empleados::findOrFail($id);
        $empleado->idciudad = $request->get('idciudad');
        $empleado->idcargo = $request->get('idcargo');
        $empleado->nombre = $request->get('nombre');
        $empleado->apellido = $request->get('apellido');
        $empleado->ci = $request->get('ci');
        $empleado->direccion = $request->get('direccion');
        $empleado->telefono = $request->get('telefono');
        $empleado->estado = $request->get('estado');
        $empleado->update();
        return Redirect::to('referenciales/empleados');
    }

    public function destroy($id)
    {
        $empleado = Empleados::findOrFail($id);
        $empleado-> estado = 'Inactivo';
        $empleado-> update();
        return Redirect::to('referenciales/empleados');
    }
}
