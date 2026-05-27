<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoImpuesto;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\TipoImpuestoFormRequest;
use DB;

class TipoImpuestoController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        if ($request)
        {
            $query = trim($request->get('searchText'));
            $tipoImpuestos = DB::table('tipo_impuesto')
                ->where('descripcion', 'LIKE', '%' . $query . '%')            
                ->orderBy('idtipoimpuesto', 'desc')
                ->paginate(7);
                
            return view('referenciales.tipo_impuesto.index', ["tipoImpuestos" => $tipoImpuestos, "searchText" => $query]);
        }
    }

    public function create()
    {
        return view("referenciales.tipo_impuesto.create");
    }

    public function store(TipoImpuestoFormRequest $request)
    {
        $tipoImpuesto = new TipoImpuesto;
        $tipoImpuesto->descripcion = $request->get('descripcion');
        $tipoImpuesto->save();
        return Redirect::to('referenciales/tipo_impuesto');
    }

    public function show($id)
    {
        return view("referenciales.tipo_impuesto.show", ["tipoImpuesto" => TipoImpuesto::findOrFail($id)]);
    }

    public function edit($id)
    {
        return view("referenciales.tipo_impuesto.edit", ["tipoImpuesto" => TipoImpuesto::findOrFail($id)]);
    }

    public function update(TipoImpuestoFormRequest $request, $id)
    {
        $tipoImpuesto = TipoImpuesto::findOrFail($id);
        $tipoImpuesto->update(['descripcion' => $request->get('descripcion')]);
        return Redirect::to('referenciales/tipo_impuesto');
    }

    public function destroy($id)
    {
        $tipoImpuesto = TipoImpuesto::findOrFail($id);
        $tipoImpuesto->estado = 'Inactivo';
        $tipoImpuesto->save();
        return Redirect::to('referenciales/tipo_impuesto');
    }
}
