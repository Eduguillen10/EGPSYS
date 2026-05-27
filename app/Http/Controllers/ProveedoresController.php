<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProveedoresFormRequest;
use App\Models\Proveedores;
use App\Models\Ciudades;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use DB;

class ProveedoresController extends Controller
{
    public function index(Request $request)
    {
        if ($request) {
            $query = trim($request->get('searchText'));
            $proveedores = DB::table('proveedores')
                ->join('ciudades', 'proveedores.idciudad', '=', 'ciudades.idciudad')
                ->select('proveedores.*', 'ciudades.descripcion as ciudad')
                ->where(function ($q) use ($query) {
                    $q->where('razonsocial', 'LIKE', '%' . $query . '%')
                        ->orWhere('ruc', 'LIKE', '%' . $query . '%');
                })
                ->orderBy('idproveedor', 'desc')
                ->paginate(7);
    
            return view('referenciales.proveedores.index', ["proveedores" => $proveedores, "searchText" => $query]);
        }
    }

    public function create()
    {
        $ciudades = Ciudades::where('estado', 'Activo')->get();
        return view("referenciales.proveedores.create", ["ciudades" => $ciudades]);
    }

    public function store(ProveedoresFormRequest $request)
    {
         // Validar la unicidad del num_documento
         $existingproveedor = Proveedores::where('ruc', $request->ruc)->first();
         if ($existingproveedor) {
             return redirect()->back()->withInput()->withErrors(['ruc' => 'El número de RUC ya está en uso.']);
         }   
        $proveedor = new Proveedores;
        $proveedor->idciudad = $request->get('idciudad');
        $proveedor->razonsocial = $request->get('razonsocial');
        $proveedor->ruc = $request->get('ruc');
        $proveedor->direccion = $request->get('direccion');
        $proveedor->telefono = $request->get('telefono');
        $proveedor->save();
        return Redirect::to('referenciales/proveedores');
    }

    public function show($id)
    {
        return view("referenciales.proveedores.show", ["proveedor" => Proveedores::findOrFail($id)]);
    }

    public function edit($id)
    {
        $proveedor = Proveedores::findOrFail($id);
        $ciudades = Ciudades::where('estado', 'Activo')->get();
        return view("referenciales.proveedores.edit", ["proveedor" => $proveedor, "ciudades" => $ciudades]);
    }

    public function update(ProveedoresFormRequest $request, $id)
    {
        $proveedor = Proveedores::findOrFail($id);
        $proveedor->update($request->only(['idciudad', 'razonsocial', 'ruc', 'direccion', 'telefono']));
        return Redirect::to('referenciales/proveedores');
    }

    public function destroy($id)
    {
        $proveedor = Proveedores::findOrFail($id);
        $proveedor->estado = 'Inactivo';
        $proveedor->save();
        return Redirect::to('referenciales/proveedores');
    }

    }
