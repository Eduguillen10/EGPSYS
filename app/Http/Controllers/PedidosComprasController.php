<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\PedidosComprasFormRequest;
use App\Models\PedidosCompras;
use App\Models\PedidosComprasDetalle;
use Illuminate\Support\Facades\Auth;
use DB;

use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;

class PedidosComprasController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        // Obtener términos de búsqueda
           $query=trim($request->get('searchText'));
           $query2=trim($request->get('searchText2'));
           $query3=trim($request->get('searchText3'));
           $query4=trim($request->get('searchText4'));
           $query5=trim($request->get('searchText5'));
           $query6=trim($request->get('searchText6'));
        // ... etc, para searchText3, searchText4, searchText5
    
        // Construir consulta SQL
        $queryBuilder = DB::table('pedidos_compras as p')
                            ->join('sucursales as s', 's.idsucursal', '=', 'p.idsucursal')
                            ->select('p.idpedidocompra', 'p.fecha', 'p.observacion', 'p.estado', 'p.usuario', 'p.idsucursal', 's.descripcion as sucursal_descripcion');
    
        // Aplicar condiciones WHERE según los términos de búsqueda
        if ($query) {
            $queryBuilder->where('p.idpedidocompra', 'LIKE', '%' . $query . '%');
        }
    
        if ($query2) {
            $queryBuilder->where('p.fecha', 'LIKE', '%' . $query2 . '%');
        }

        if ($query6) {
            $queryBuilder->where('p.usuario', 'LIKE', '%' . $query6 . '%');
        }
    
        if ($query3) {
            $queryBuilder->where('p.idsucursal', 'LIKE', '%' . $query3 . '%');
        }

        if ($query4) {
            $queryBuilder->where('p.observacion', 'LIKE', '%' . $query4 . '%');
        }

        if ($query5) {
            $queryBuilder->where('p.estado', 'LIKE', '%' . $query5 . '%');
        }
        // ... añadir las demás condiciones WHERE de manera similar. 
    
        // Ordenar y paginar resultados
        $pedidos_compras = $queryBuilder->orderBy('p.idpedidocompra', 'desc')
                                        ->paginate(7); // Paginar directamente
    
        // Obtener total de registros (con los filtros aplicados)
        $total = $pedidos_compras->total();
    
        // Retornar vista con datos
        return view('compras.pedido.index', [
            "pedidos_compras" => $pedidos_compras,
            "searchText" => $query,
            "searchText2" => $query2,
            "searchText3" => $query3,
            "searchText4" => $query4,
            "searchText5" => $query5,
            "searchText6" => $query6,
            // ... etc para otros searchText
            "total" => $total
        ]);
    }
    public function create()
    {
        $suc = Auth::user()->trabaja_sucursal;
        $sucursales=DB::table('sucursales as s')
        ->select('s.idsucursal','s.descripcion')
        ->where('idsucursal', '=', $suc)
        ->first();      
        $productos = DB::table('productos as prod')
        ->join('marcas as m', 'm.idmarca', '=', 'prod.idmarca')
        ->select('prod.descripcion AS productos','prod.idproducto','m.descripcion AS marcas')
        ->get();
        return view("compras.pedido.create",["productos"=>$productos,"sucursales"=>$sucursales]);
    }

    public function store (PedidosComprasFormRequest $request)
    {
        try {
            DB::beginTransaction();
    
            $pedidos_compras = new PedidosCompras;
            $pedidos_compras->observacion = $request->get('observacion');
            $pedidos_compras->usuario = $request->get('usuario');
            $pedidos_compras->idsucursal = $request->get('idsucursal');
            $mytime = Carbon::now('America/Asuncion');
            $pedidos_compras->fecha = $mytime->toDateTimeString();
            $pedidos_compras->estado = 'Pendiente';
            $pedidos_compras->save();
    
            $idproducto = $request->get('idproducto');
            $cantidad = $request->get('cantidad');
            $cont = 0;
            $items = 1;
            //return dd($pedidos_compras);
            $idpedidocompra=$pedidos_compras->idpedidocompra;
            
            //return dd($idpedidocompra);

            while ($cont < count($idproducto)) {
                $pedidos_compras_detalle = new PedidosComprasDetalle();
                $pedidos_compras_detalle->idpedidocompra = $idpedidocompra;
                $pedidos_compras_detalle->items = $items;
                $pedidos_compras_detalle->idproducto = $idproducto[$cont];
                $pedidos_compras_detalle->cantidad = $cantidad[$cont];
                $pedidos_compras_detalle->save();

                // dd('Checkpoint 1');
                // dd($pedidos_compras_detalle);

                $cont = $cont + 1;
                $items++;
            }
            
            DB::commit();
            

        } catch (Exception $e) {
            DB::rollback();
        }
    
        return Redirect::to('compras/pedido');
    }
    
    public function show($id)
    {
        $pedidos_compras = DB::table('pedidos_compras as p')
            ->join('sucursales as s', 's.idsucursal', '=', 'p.idsucursal')
            ->select('p.idpedidocompra', 'p.fecha', 'p.observacion', 'p.estado', 'p.usuario', 'p.idsucursal', 's.descripcion as sucursal_descripcion')
            ->where('p.idpedidocompra', '=', $id)
            ->first();
    
        $detallePedido = DB::table('pedidos_compras_detalle as pd')
            ->join('productos as prod', 'pd.idproducto', '=', 'prod.idproducto')
            ->select('prod.descripcion as producto', 'pd.cantidad', 'items', 'prod.idproducto')
            ->where('pd.idpedidocompra', '=', $id)
            ->get();
    
        return view("compras.pedido.show", ["pedidos_compras" => $pedidos_compras, "detallePedido" => $detallePedido]);
    }

    public function destroy($id)
    {
        $pedidos_compras=PedidosCompras::findOrFail($id);
        $pedidos_compras->Estado='Cancelado';
        $pedidos_compras->update();
        return Redirect::to('compras/pedido');
    }
}
