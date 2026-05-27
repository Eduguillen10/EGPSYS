<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ciudades; // Asegúrate de importar el modelo Ciudades
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\CiudadesFormRequest; // Asegúrate de importar la clase CiudadesFormRequest
use DB;

class CiudadesController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        if ($request)
        {
            $query = trim($request->get('searchText'));
            $ciudades = DB::table('ciudades')
                ->where('descripcion', 'LIKE', '%' . $query . '%')            
                ->orderBy('idciudad', 'desc')
                ->paginate(7);
            return view('referenciales.ciudades.index', ["ciudades" => $ciudades, "searchText" => $query]);
        }
    }

    public function create()
    {
        return view("referenciales.ciudades.create");
    }

    public function store(CiudadesFormRequest $request)
    {
        $ciudades = new Ciudades;
        $ciudades->descripcion = $request->get('descripcion');
        $ciudades->save();
        return Redirect::to('referenciales/ciudades');
    }

    public function show($id)
    {
        return view("referenciales.ciudades.show", ["ciudades" => Ciudades::findOrFail($id)]);
    }

    public function edit($id)
    {
        return view("referenciales.ciudades.edit", ["ciudad" => Ciudades::findOrFail($id)]);
    }

    public function update(CiudadesFormRequest $request, $id)
    {
        $ciudades = Ciudades::findOrFail($id);
        $ciudades->update(['descripcion' => $request->get('descripcion')]);
        return Redirect::to('referenciales/ciudades');
    }

    public function destroy($id)
    {
        $ciudades = Ciudades::findOrFail($id);
        $ciudades->estado = 'Inactivo';
        $ciudades->save();
        return Redirect::to('referenciales/ciudades');
    }
}
