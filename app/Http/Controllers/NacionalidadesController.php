<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\NacionalidadesFormRequest;
use App\Models\Nacionalidades;
use Illuminate\Support\Facades\Redirect;
use DB;

class NacionalidadesController extends Controller
{
    public function index(Request $request)
    {
        if ($request) {
            $query = trim($request->get('searchText'));
            $nacionalidades = DB::table('nacionalidades')
                ->where('descripcion', 'LIKE', '%' . $query . '%')
                ->orderBy('idnacionalidad', 'desc')
                ->paginate(7);

            return view('referenciales.nacionalidades.index', ["nacionalidades" => $nacionalidades, "searchText" => $query]);
        }
    }

    public function create()
    {
        return view('referenciales.nacionalidades.create');
    }

    public function store(NacionalidadesFormRequest $request)
    {
        $nacionalidades = new Nacionalidades;
        $nacionalidades->descripcion = $request->get('descripcion');
        $nacionalidades->save();

        return Redirect::to('referenciales/nacionalidades');
    }

    public function show($id)
    {
        $nacionalidades = Nacionalidades::findOrFail($id);
        return view('referenciales.nacionalidades.show', ['nacionalidades' => $nacionalidades]);
    }

    public function edit($id)
    {
        $nacionalidades = Nacionalidades::findOrFail($id);
        return view('referenciales.nacionalidades.edit', ['nacionalidades' => $nacionalidades]);
    }

    public function update(NacionalidadesFormRequest $request, $id)
    {
        $nacionalidades = Nacionalidades::findOrFail($id);
        $nacionalidades->update(['descripcion' => $request->get('descripcion')]);

        return Redirect::to('referenciales/nacionalidades');
    }

    public function destroy($id)
    {
        $nacionalidades = Nacionalidades::findOrFail($id);
        $nacionalidades->estado = 'Inactivo';
        $nacionalidades->save();

        return Redirect::to('referenciales/nacionalidades');
    }
}
