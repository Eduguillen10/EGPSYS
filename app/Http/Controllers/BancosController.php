<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bancos;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\BancosFormRequest;
use DB;

class BancosController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        if ($request)
        {
            $query=trim($request->get('searchText'));
            $bancos=DB::table('bancos')
            ->where('descripcion','LIKE','%'.$query.'%')            
            ->orderBy('idbanco','desc')
            ->paginate(7);
            return view('referenciales.bancos.index',["bancos"=>$bancos,"searchText"=>$query]);
        }
    }

    public function create()
    {
        return view("referenciales.bancos.create");
    }

    public function store(BancosFormRequest $request)
    {
        $bancos=new Bancos;
        $bancos->descripcion=$request->get('descripcion');
        $bancos->save();
        return Redirect::to('referenciales/bancos');
    }

    public function show($id)
    {
        return view("referenciales.bancos.show",["bancos"=>Bancos::findOrFail($id)]);
    }

    public function edit($id)
    {
        return view("referenciales.bancos.edit",["banco"=>Bancos::findOrFail($id)]);
    }

    public function update(BancosFormRequest $request, $id)
    {
    $bancos = Bancos::findOrFail($id);
    $bancos->update(['descripcion' => $request->get('descripcion')]);
    return Redirect::to('referenciales/bancos');
    }

    public function destroy($id)
    {
        $bancos=Bancos::findOrFail($id);
        $bancos->estado = 'Inactivo';
        $bancos->save();
        return Redirect::to('referenciales/bancos');
    }
}
