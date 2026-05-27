<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormaCobro;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\FormaCobroFormRequest;
use DB;

class FormaCobroController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        if ($request)
        {
            $query = trim($request->get('searchText'));
            $formacobros = DB::table('formacobro')
                ->where('descripcion', 'LIKE', '%' . $query . '%')            
                ->orderBy('id_formacobro', 'desc')
                ->paginate(7);
            
            return view('referenciales.formacobro.index', ["formacobros" => $formacobros, "searchText" => $query]);
        }
    }

    public function create()
    {
        return view("referenciales.formacobro.create");
    }

    public function store(FormaCobroFormRequest $request)
    {
        $formaCobro = new FormaCobro;
        $formaCobro->descripcion = $request->get('descripcion');
        $formaCobro->save();
        return Redirect::to('referenciales/formacobro');
    }

    public function show($id)
    {
        return view("referenciales.formacobro.show", ["formaCobro" => FormaCobro::findOrFail($id)]);
    }

    public function edit($id)
    {
        return view("referenciales.formacobro.edit", ["formacobro" => FormaCobro::findOrFail($id)]);
    }

    public function update(FormaCobroFormRequest $request, $id)
    {
        $formaCobro = FormaCobro::findOrFail($id);
        $formaCobro->update(['descripcion' => $request->get('descripcion')]);
        return Redirect::to('referenciales/formacobro');
    }

    public function destroy($id)
    {
        $formaCobro = FormaCobro::findOrFail($id);
        $formaCobro->estado = 'Inactivo';
        $formaCobro->save();
        return Redirect::to('referenciales/formacobro');
    }
}
