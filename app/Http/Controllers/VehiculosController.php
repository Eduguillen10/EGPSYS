<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculos;
use App\Http\Requests\VehiculosFormRequest;
use Illuminate\Support\Facades\Redirect;

class VehiculosController extends Controller
{
    public function index(Request $request)
    {
        $searchText = $request->get('searchText');
        $vehiculos = Vehiculos::where(function ($query) use ($searchText) {
                $query->where('nrochapa', 'LIKE', '%' . $searchText . '%')
                    ->orWhere('color', 'LIKE', '%' . $searchText . '%')
                    ->orWhere('chasis', 'LIKE', '%' . $searchText . '%')
                    ->orWhere('modelo', 'LIKE', '%' . $searchText . '%');
            })
            ->paginate(7);

        return view('referenciales.vehiculos.index', compact('vehiculos', 'searchText'));
    }

    public function create()
    {
        return view('referenciales.vehiculos.create');
    }

    public function store(VehiculosFormRequest $request)
    {
        Vehiculos::create($request->all());
        return Redirect::to('referenciales/vehiculos');
    }

    public function show($id)
    {
        $vehiculo = Vehiculos::findOrFail($id);
        return view('referenciales.vehiculos.show', compact('vehiculo'));
    }

    public function edit($id)
    {
        $vehiculo = Vehiculos::findOrFail($id);
        return view('referenciales.vehiculos.edit', compact('vehiculo'));
    }

    public function update(VehiculosFormRequest $request, $id)
    {
        $vehiculo = Vehiculos::findOrFail($id);
        $vehiculo->update($request->all());
        return Redirect::to('referenciales/vehiculos');
    }

    public function destroy($id)
    {
        $vehiculo = Vehiculos::findOrFail($id);
        $vehiculo->estado = 'Inactivo';
        $vehiculo->save();
        return Redirect::to('referenciales/vehiculos');
    }
}
