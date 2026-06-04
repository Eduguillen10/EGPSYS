<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipoAjusteFormRequest;
use App\Models\TipoAjuste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class TipoAjusteController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->get('searchText'));
        $tipos = DB::table('tipo_ajuste')
            ->where('descripcion', 'LIKE', '%' . $query . '%')
            ->orderByDesc('idtipo_ajuste')
            ->paginate(7);

        return view('referenciales.tipo_ajuste.index', [
            'tipos' => $tipos,
            'searchText' => $query,
        ]);
    }

    public function create()
    {
        return view('referenciales.tipo_ajuste.create');
    }

    public function store(TipoAjusteFormRequest $request)
    {
        TipoAjuste::create([
            'descripcion' => $request->input('descripcion'),
            'estado' => 'Activo',
        ]);

        return Redirect::to('referenciales/tipo_ajuste');
    }

    public function show($id)
    {
        return view('referenciales.tipo_ajuste.show', [
            'tipo' => TipoAjuste::findOrFail($id),
        ]);
    }

    public function edit($id)
    {
        return view('referenciales.tipo_ajuste.edit', [
            'tipo' => TipoAjuste::findOrFail($id),
        ]);
    }

    public function update(TipoAjusteFormRequest $request, $id)
    {
        $tipo = TipoAjuste::findOrFail($id);
        $tipo->update([
            'descripcion' => $request->input('descripcion'),
        ]);

        return Redirect::to('referenciales/tipo_ajuste');
    }

    public function destroy($id)
    {
        $tipo = TipoAjuste::findOrFail($id);
        $tipo->estado = 'Inactivo';
        $tipo->save();

        return Redirect::to('referenciales/tipo_ajuste');
    }
}
