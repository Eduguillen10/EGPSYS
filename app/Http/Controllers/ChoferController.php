<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChoferesFormRequest;
use App\Models\Chofer;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class ChoferController extends Controller
{
    public function index(Request $request)
    {
        if ($request) {
            $query = trim($request->get('searchText'));
            $choferes = Chofer::where(function ($q) use ($query) {
                    $q->where('nombre', 'LIKE', '%' . $query . '%')
                        ->orWhere('apellido', 'LIKE', '%' . $query . '%');
                })
                ->orderBy('idchofer', 'desc')
                ->paginate(7);
    
            return view('referenciales.choferes.index', ["choferes" => $choferes, "searchText" => $query]);
        }
    }

    public function create()
    {
        return view("referenciales.choferes.create");
    }

    public function store(ChoferesFormRequest $request)
    {
        // Validacion para la unicidad del campo ci
        $existingChofer = Chofer::where('ci', $request->ci)->first();
        if ($existingChofer) {
            return redirect()->back()->withInput()->withErrors(['ci' => 'El número de C.I. ya está en uso.']);
        }    
         // Validacion para la unicidad del campo ruc
         $existingRucChofer = Chofer::where('ruc', $request->ruc)->first();
         if ($existingRucChofer) {
             return redirect()->back()->withInput()->withErrors(['ruc' => 'El RUC ya está en uso.']);
         }    

        $chofer = new Chofer;
        $chofer->nombre = $request->get('nombre');
        $chofer->apellido = $request->get('apellido');
        $chofer->ci = $request->get('ci');
        $chofer->ruc = $request->get('ruc');
        $chofer->direccion = $request->get('direccion');
        $chofer->telefono = $request->get('telefono');
        $chofer->email = $request->get('email');
        $chofer->save();
        return Redirect::to('referenciales/choferes');
    }

    public function show($id)
    {
        $chofer = Chofer::findOrFail($id);
        return view("referenciales.choferes.show", ["chofer" => $chofer]);
    }

    public function edit($id)
    {
        $chofer = Chofer::findOrFail($id);
        return view("referenciales.choferes.edit", ["chofer" => $chofer]);
    }

    public function update(ChoferesFormRequest $request, $id)
    {
        $chofer = Chofer::findOrFail($id);
        $chofer->nombre = $request->get('nombre');
        $chofer->apellido = $request->get('apellido');
        $chofer->ci = $request->get('ci');
        $chofer->ruc = $request->get('ruc');
        $chofer->direccion = $request->get('direccion');
        $chofer->telefono = $request->get('telefono');
        $chofer->email = $request->get('email');
        $chofer->update();
        return Redirect::to('referenciales/choferes');
    }

    public function destroy($id)
    {
        $chofer = Chofer::findOrFail($id);
        $chofer->estado = 'Inactivo';
        $chofer->save();
        return Redirect::to('referenciales/choferes');
    }
}
