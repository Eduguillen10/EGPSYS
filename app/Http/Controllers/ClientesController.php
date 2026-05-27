<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientesFormRequest;
use App\Models\Clientes;
use App\Models\Ciudades;
use App\Models\TiposDocumentos;
use App\Models\TiposClientes;
use App\Models\Nacionalidades;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use DB;

class ClientesController extends Controller
{
    public function index(Request $request)
    {
        $query = trim($request->get('searchText'));

        // SUBQUERY: total ventas por cliente (ignora anuladas)
        $ventasSub = DB::table('ventas')
            ->select('idcliente', DB::raw('SUM(totalventa) as total_ventas'))
            ->whereNotIn('estado', ['A', 'Anulado', 'Anulada', 'Cancelado', 'Cancelada'])
            ->groupBy('idcliente');

        // SUBQUERY: total notas de crédito por cliente (ignora anuladas)
        $ncSub = DB::table('nota_credito_venta')
            ->select('idcliente', DB::raw('SUM(totalventa) as total_nc'))
            ->whereNotIn('estado', ['A', 'Anulado', 'Anulada', 'Cancelado', 'Cancelada'])
            ->groupBy('idcliente');

        $ndSub = DB::table('nota_debito_venta')
            ->select('idcliente', DB::raw('SUM(totalventa) as total_nd'))
            ->whereNotIn('estado', ['A', 'Anulado', 'Anulada', 'Cancelado', 'Cancelada'])
            ->groupBy('idcliente');

        $clientes = DB::table('clientes')
            ->join('ciudades', 'clientes.idciudad', '=', 'ciudades.idciudad')
            ->join('tipo_cliente as tc', 'clientes.idtipo_cliente', '=', 'tc.idtipo_cliente')
            ->join('nacionalidades as n', 'clientes.idnacionalidad', '=', 'n.idnacionalidad')
            ->join('tipos_documentos as td', 'clientes.idtipodocumento', '=', 'td.idtipodocumento')

            // en vez de leftJoin('ventas') que multiplica filas, usamos subqueries
            ->leftJoinSub($ventasSub, 'vsum', function ($join) {
                $join->on('clientes.idcliente', '=', 'vsum.idcliente');
            })
            ->leftJoinSub($ncSub, 'ncsum', function ($join) {
                $join->on('clientes.idcliente', '=', 'ncsum.idcliente');
            })
            ->leftJoinSub($ndSub, 'ndsum', function ($join) {
                $join->on('clientes.idcliente', '=', 'ndsum.idcliente');
            })

            ->select(
                'clientes.idcliente',
                'clientes.nombre',
                'clientes.num_documento',
                'clientes.direccion',
                'clientes.telefono',
                'clientes.email',
                'clientes.estado',
                'clientes.idtipo_cliente',
                'clientes.idnacionalidad',
                'clientes.idtipodocumento',
                'ciudades.descripcion as ciudad',
                'tc.descripcion as tipo_cliente',
                'n.descripcion as nacionalidad',
                'td.descripcion as tipo_documento',
                'clientes.modo_clasificacion',

                // TOTAL NETO: ventas + notas de debito - notas de credito
                DB::raw('COALESCE(vsum.total_ventas, 0) + COALESCE(ndsum.total_nd, 0) - COALESCE(ncsum.total_nc, 0) as total_ventas'),

                // Clasificación basada en total neto
                DB::raw("
                CASE
                    WHEN (COALESCE(vsum.total_ventas, 0) + COALESCE(ndsum.total_nd, 0) - COALESCE(ncsum.total_nc, 0)) > 100000000 THEN 'Platino'
                    WHEN (COALESCE(vsum.total_ventas, 0) + COALESCE(ndsum.total_nd, 0) - COALESCE(ncsum.total_nc, 0)) >= 50000000 THEN 'Oro'
                    WHEN (COALESCE(vsum.total_ventas, 0) + COALESCE(ndsum.total_nd, 0) - COALESCE(ncsum.total_nc, 0)) >= 10000000 THEN 'Plata'
                    ELSE 'Bronce'
                END as clasificacion
            ")
            )

            // IMPORTANTE: agrupar el where/orWhere con paréntesis
            ->where(function ($q) use ($query) {
                $q->where('clientes.nombre', 'LIKE', '%' . $query . '%')
                    ->orWhere('clientes.num_documento', 'LIKE', '%' . $query . '%');
            })
            ->orderBy('clientes.idcliente', 'desc')
            ->paginate(7);

        return view('referenciales.clientes.index', [
            "clientes" => $clientes,
            "searchText" => $query
        ]);
    }

    public function create()
    {
        $cliente = new Clientes();
        $ciudades = Ciudades::where('estado', 'Activo')->get();
        $tiposDocumentos = TiposDocumentos::where('estado', 'Activo')->get();
        $tiposClientes = TiposClientes::where('estado', 'Activo')->get();
        $nacionalidades = Nacionalidades::where('estado', 'Activo')->get();

        return view("referenciales.clientes.create", ["cliente" => $cliente, "ciudades" => $ciudades, "tiposDocumentos" => $tiposDocumentos, "tiposClientes" => $tiposClientes, "nacionalidades" => $nacionalidades]);
    }

    public function store(ClientesFormRequest $request)
    {
        // Validar la unicidad del num_documento
        $existingcliente = Clientes::where('num_documento', $request->num_documento)->first();
        if ($existingcliente) {
            return redirect()->back()->withInput()->withErrors(['num_documento' => 'El Número de Documento ya está en uso.']);
        }
        $cliente = new Clientes;
        $cliente->idnacionalidad = $request->get('idnacionalidad');
        $cliente->idciudad = $request->get('idciudad');
        $cliente->idtipodocumento = $request->get('idtipodocumento');
        $cliente->nombre = $request->get('nombre');
        $cliente->num_documento = $request->get('num_documento');
        $cliente->direccion = $request->get('direccion');
        $cliente->telefono = $request->get('telefono');
        $cliente->email = $request->get('email');

        if ($request->filled('idtipo_cliente')) {
            $cliente->idtipo_cliente = $request->get('idtipo_cliente');
            $cliente->modo_clasificacion = 'manual';
        } else {
            $cliente->idtipo_cliente = $this->determinarClasificacion($cliente->idcliente);
            $cliente->modo_clasificacion = 'automatico';
        }

        $cliente->save();
        return Redirect::to('referenciales/clientes');
    }

    public function show($id)
    {
        return view("referenciales.clientes.show", ["cliente" => Clientes::findOrFail($id)]);
    }

    public function edit($id)
    {
        $cliente = Clientes::findOrFail($id); // Reemplaza "Cliente" con el nombre correcto de tu modelo de clientes
        $ciudades = Ciudades::where('estado', 'Activo')->get();
        $tiposDocumentos = TiposDocumentos::where('estado', 'Activo')->get();
        $tiposClientes = TiposClientes::where('estado', 'Activo')->get();
        $nacionalidades = Nacionalidades::where('estado', 'Activo')->get();

        return view("referenciales.clientes.edit", ["cliente" => $cliente, "ciudades" => $ciudades, "tiposDocumentos" => $tiposDocumentos, "tiposClientes" => $tiposClientes, "nacionalidades" => $nacionalidades]);
    }

    public function update(ClientesFormRequest $request, $id)
    {
        $cliente = Clientes::findOrFail($id);
        $cliente->idtipodocumento = $request->get('idtipodocumento');
        $cliente->idnacionalidad = $request->get('idnacionalidad');
        $cliente->idciudad = $request->get('idciudad');
        $cliente->nombre = $request->get('nombre');
        $cliente->num_documento = $request->get('num_documento');
        $cliente->direccion = $request->get('direccion');
        $cliente->telefono = $request->get('telefono');
        $cliente->email = $request->get('email');

        if ($request->filled('idtipo_cliente')) {
            $cliente->idtipo_cliente = $request->get('idtipo_cliente');
            $cliente->modo_clasificacion = 'manual';
        } else {
            $cliente->idtipo_cliente = $this->determinarClasificacion($cliente->idcliente);
            $cliente->modo_clasificacion = 'automatico';
        }

        $cliente->update();
        return Redirect::to('referenciales/clientes');
    }

    public function destroy($id)
    {
        $cliente = Clientes::findOrFail($id);
        $cliente->estado = 'Inactivo';
        $cliente->save();
        return Redirect::to('referenciales/clientes');
    }

    private function determinarClasificacion($idcliente)
    {
        $ventas = DB::table('ventas')
            ->where('idcliente', $idcliente)
            ->whereNotIn('estado', ['A', 'Anulado', 'Anulada', 'Cancelado', 'Cancelada'])
            ->sum('totalventa');

        $nc = DB::table('nota_credito_venta')
            ->where('idcliente', $idcliente)
            ->whereNotIn('estado', ['A', 'Anulado', 'Anulada', 'Cancelado', 'Cancelada'])
            ->sum('totalventa');

        $nd = DB::table('nota_debito_venta')
            ->where('idcliente', $idcliente)
            ->whereNotIn('estado', ['A', 'Anulado', 'Anulada', 'Cancelado', 'Cancelada'])
            ->sum('totalventa');

        $totalNeto = (float) $ventas + (float) $nd - (float) $nc;

        if ($totalNeto > 100000000)
            return 1;// Platino
        if ($totalNeto >= 50000000)
            return 3;// Oro
        if ($totalNeto >= 10000000)
            return 4;// Plata
        return 5;// Bronce
    }
}
