<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rubros;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\RubrosFormRequest;
use DB;

class RubrosController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        if ($request)
        {
            $query=trim($request->get('searchText'));
            $rubros=DB::table('rubros')
            ->where('descripcion','LIKE','%'.$query.'%')            
            ->orderBy('idrubro','desc')
            ->paginate(7);
            return view('referenciales.rubros.index',["rubros"=>$rubros,"searchText"=>$query]);
        }
    }

    public function create()
    {
        return view("referenciales.rubros.create");
    }

    public function store(RubrosFormRequest $request)
    {
        $rubros=new Rubros;
        $rubros->descripcion=$request->get('descripcion');
        $rubros->save();
        return Redirect::to('referenciales/rubros');
    }

    public function show($id)
    {
        return view("referenciales.rubros.show",["rubros"=>Rubros::findOrFail($id)]);
    }

    public function edit($id)
    {
        return view("referenciales.rubros.edit",["rubro"=>Rubros::findOrFail($id)]);
    }

    public function update(RubrosFormRequest $request, $id)
    {
    $rubros = Rubros::findOrFail($id);
    $rubros->update(['descripcion' => $request->get('descripcion')]);
    return Redirect::to('referenciales/rubros');
    }

    public function destroy($id)
    {
        $rubros=Rubros::findOrFail($id);  
        $rubros->estado = 'Inactivo';
        $rubros->save();
        return Redirect::to('referenciales/rubros');
    }
}
