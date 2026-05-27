<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Motivo;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\MotivoFormRequest;
use DB;

class MotivoController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        if ($request)
        {
            $query = trim($request->get('searchText'));
            $motivos = DB::table('motivo')
                ->where('descripcion', 'LIKE', '%' . $query . '%')            
                ->orderBy('idmotivo', 'desc')
                ->paginate(7);
            
            return view('referenciales.motivo.index', ["motivos" => $motivos, "searchText" => $query]);
        }
    }

    public function create()
    {
        return view("referenciales.motivo.create");
    }

    public function store(MotivoFormRequest $request)
    {
        $motivo = new Motivo;
        $motivo->descripcion = $request->get('descripcion');
        $motivo->save();
        return Redirect::to('referenciales/motivo');
    }

    public function show($id)
    {
        return view("referenciales.motivo.show", ["motivo" => Motivo::findOrFail($id)]);
    }

    public function edit($id)
    {
        return view("referenciales.motivo.edit", ["motivo" => Motivo::findOrFail($id)]);
    }

    public function update(MotivoFormRequest $request, $id)
    {
        $motivo = Motivo::findOrFail($id);
        $motivo->update(['descripcion' => $request->get('descripcion')]);
        return Redirect::to('referenciales/motivo');
    }

    public function destroy($id)
    {
        $motivo = Motivo::findOrFail($id);
        $motivo->estado = 'Inactivo';
        $motivo->save();
        return Redirect::to('referenciales/motivo');
    }
}
