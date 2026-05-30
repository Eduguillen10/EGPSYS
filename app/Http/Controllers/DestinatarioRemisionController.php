<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestinatariosRemisionFormRequest;
use App\Models\DestinatarioRemision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class DestinatarioRemisionController extends Controller
{
    public function index(Request $request)
    {
        $searchText = trim((string) $request->get('searchText', ''));

        $destinatarios = DestinatarioRemision::where(function ($query) use ($searchText): void {
                $query->where('nombre', 'LIKE', '%' . $searchText . '%')
                    ->orWhere('documento', 'LIKE', '%' . $searchText . '%')
                    ->orWhere('telefono', 'LIKE', '%' . $searchText . '%');
            })
            ->orderByDesc('iddestinatario_remision')
            ->paginate(7);

        return view('referenciales.destinatarios_remision.index', compact('destinatarios', 'searchText'));
    }

    public function create()
    {
        return view('referenciales.destinatarios_remision.create');
    }

    public function store(DestinatariosRemisionFormRequest $request)
    {
        DestinatarioRemision::create($request->validated() + ['estado' => 'Activo']);

        return Redirect::to('referenciales/destinatarios_remision');
    }

    public function show(int $id)
    {
        $destinatario = DestinatarioRemision::findOrFail($id);

        return view('referenciales.destinatarios_remision.show', compact('destinatario'));
    }

    public function edit(int $id)
    {
        $destinatario = DestinatarioRemision::findOrFail($id);

        return view('referenciales.destinatarios_remision.edit', compact('destinatario'));
    }

    public function update(DestinatariosRemisionFormRequest $request, int $id)
    {
        $destinatario = DestinatarioRemision::findOrFail($id);
        $destinatario->update($request->validated());

        return Redirect::to('referenciales/destinatarios_remision');
    }

    public function destroy(int $id)
    {
        $destinatario = DestinatarioRemision::findOrFail($id);
        $destinatario->estado = 'Inactivo';
        $destinatario->save();

        return Redirect::to('referenciales/destinatarios_remision');
    }
}
