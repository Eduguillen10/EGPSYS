<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransportistasFormRequest;
use App\Models\Transportista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TransportistaController extends Controller
{
    public function index(Request $request)
    {
        $searchText = trim((string) $request->get('searchText', ''));

        $transportistas = Transportista::where(function ($query) use ($searchText): void {
                $query->where('nombre', 'LIKE', '%' . $searchText . '%')
                    ->orWhere('documento', 'LIKE', '%' . $searchText . '%')
                    ->orWhere('telefono', 'LIKE', '%' . $searchText . '%');
            })
            ->orderByDesc('idtransportista')
            ->paginate(7);

        return view('referenciales.transportistas.index', compact('transportistas', 'searchText'));
    }

    public function create()
    {
        return view('referenciales.transportistas.create');
    }

    public function store(TransportistasFormRequest $request)
    {
        Transportista::create($request->validated() + ['estado' => 'Activo']);

        return Redirect::to('referenciales/transportistas');
    }

    public function show(int $id)
    {
        $transportista = Transportista::findOrFail($id);

        return view('referenciales.transportistas.show', compact('transportista'));
    }

    public function edit(int $id)
    {
        $transportista = Transportista::findOrFail($id);

        return view('referenciales.transportistas.edit', compact('transportista'));
    }

    public function update(TransportistasFormRequest $request, int $id)
    {
        $transportista = Transportista::findOrFail($id);
        $transportista->update($request->validated());

        return Redirect::to('referenciales/transportistas');
    }

    public function destroy(int $id)
    {
        $transportista = Transportista::findOrFail($id);
        $transportista->estado = 'Inactivo';
        $transportista->save();

        return Redirect::to('referenciales/transportistas');
    }
}
