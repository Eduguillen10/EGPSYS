<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PresupuestosComprasFormRequest;
use App\Models\PresupuestosCompras;
use App\Models\PresupuestosComprasDetalle;
use App\Models\PedidosCompras;
use App\Models\PedidosComprasDetalle;
use App\Models\Productos;
use App\Models\Proveedores;
use App\Models\Sucursales;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use DB;
use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;

class PresupuestosComprasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = trim($request->get('searchText'));
        $query2 = trim($request->get('searchText2'));
        $query3 = trim($request->get('searchText3'));
        $query4 = trim($request->get('searchText4'));
        $query5 = trim($request->get('searchText5'));
        $query6 = trim($request->get('searchText6'));
        $query7 = trim($request->get('searchText7'));

        $queryBuilder = DB::table('presupuestos_compras as pc')
                            ->join('sucursales as s', 'pc.idsucursal', '=', 's.idsucursal')
                            ->join('proveedores as p', 'pc.idproveedor', '=', 'p.idproveedor')
                            ->join('users as u', 'pc.idusuario', '=', 'u.id')
                            ->leftJoin('pedidos_compras as pedido', 'pc.idpedidocompra', '=', 'pedido.idpedidocompra')
                            ->select(
                                'pc.idpresupuestocompra', 
                                's.idsucursal',
                                's.descripcion', 
                                'p.idproveedor',
                                'p.razonsocial', 
                                'p.ruc', 
                                'pc.idpedidocompra', 
                                'pc.fecha', 
                                'pc.fechavalidez', 
                                'pc.montoiva10', 
                                'pc.montoiva5', 
                                'pc.montogravada10', 
                                'pc.montogravada5', 
                                'pc.montoexenta',
                                'pc.montopresupuesto_compra',
                                'pc.observacion',
                                'u.name as usuario',
                                'pc.estado'
                            );
        // Aplicar condiciones WHERE según los términos de búsqueda
        if ($query) {
            $queryBuilder->where('pc.idpresupuestocompra', 'LIKE', '%' . $query . '%');
        }
    
        if ($query2) {
            $queryBuilder->where('pc.fecha', 'LIKE', '%' . $query2 . '%');
        }

        if ($query3) {
            $queryBuilder->where('s.descripcion', 'LIKE', '%' . $query3 . '%');
        }

        if ($query4) {
            $queryBuilder->where('p.razonsocial', 'LIKE', '%' . $query4 . '%');
        }

        if ($query5) {
            $queryBuilder->where('pc.idpedidocompra', 'LIKE', '%' . $query5 . '%');
        }

        if ($query6) {
            $queryBuilder->where('p.ruc', 'LIKE', '%' . $query6 . '%');
        }

        if ($query7) {
            $queryBuilder->where('pc.estado', 'LIKE', '%' . $query7 . '%');
        }
      
        $presupuestos_compras = $queryBuilder->orderBy('pc.idpresupuestocompra', 'desc')
        ->groupBy(
            'pc.idpresupuestocompra', 
            's.idsucursal',
            's.descripcion', 
            'p.idproveedor', 
            'p.razonsocial', 
            'p.ruc', 
            'pc.idpedidocompra', 
            'pc.fecha', 
            'pc.fechavalidez', 
            'pc.montoiva10', 
            'pc.montoiva5', 
            'pc.montogravada10', 
            'pc.montogravada5', 
            'pc.montoexenta',
            'pc.montopresupuesto_compra',
            'pc.observacion',
            'u.name',
            'pc.estado'
        )
        ->paginate(7); // Paginar directamente
       
        // Obtener total de registros (con los filtros aplicados)
        $total = $presupuestos_compras->total();

        return view('compras.presupuesto.index', [
            "presupuestos_compras" => $presupuestos_compras,
            "searchText" => $query,
            "searchText2" => $query2,
            "searchText3" => $query3,
            "searchText4" => $query4,
            "searchText5" => $query5,
            "searchText6" => $query6,
            "searchText7" => $query7,
            // ... etc para otros searchText
            "total" => $total
        ]);
}

public function create()
{
    $suc = Auth::user()->trabaja_sucursal;
    $fecha = date('Y-m-d');
    $proveedores = Proveedores::all();
    //$productos = Productos::all();
    $sucursales=DB::table('sucursales as s')
    ->select('s.idsucursal','s.descripcion')
    ->where('idsucursal', '=', $suc)
    ->first();
    $productos = DB::table('productos as prod')
    ->join('marcas as m', 'm.idmarca', '=', 'prod.idmarca')
    ->select('prod.descripcion AS productos','prod.idproducto','m.descripcion AS marcas')
    ->get();
    $pedidos=DB:: table('pedidos_compras as pc')
    ->join('users as u', 'pc.idusuario', '=', 'u.id')
    ->select('pc.*', 'u.name as usuario')
    ->where('idsucursal', '=', $suc)
    ->where('estado', '=', 'Pendiente')
    ->get();
    return view('compras.presupuesto.create', compact('suc', 'fecha', 'proveedores', 'productos','sucursales','pedidos'));
}

        

public function store (PresupuestosComprasFormRequest $request)
{
    $usuario = Auth::user();
    if (!$usuario || !$usuario->trabaja_sucursal) {
        return Redirect::back()
            ->withInput()
            ->withErrors(['idsucursal' => 'Debe seleccionar una sucursal antes de registrar el presupuesto.']);
    }

    try {
        DB::beginTransaction();
            $pedido = DB::table('pedidos_compras')
                ->where('idpedidocompra', $request->get('idpedidocompra'))
                ->where('idsucursal', $usuario->trabaja_sucursal)
                ->where('estado', 'Pendiente')
                ->first();

            if (!$pedido) {
                DB::rollBack();
                return Redirect::back()
                    ->withInput()
                    ->withErrors(['idpedidocompra' => 'El pedido debe estar pendiente y pertenecer a la sucursal actual.']);
            }

            $presupuesto=new PresupuestosCompras;
            $presupuesto->idproveedor=$request->get('idproveedor');
            $presupuesto->idsucursal=$usuario->trabaja_sucursal;                
            $presupuesto->idusuario=$usuario->id;
            $presupuesto->idpedidocompra=$request->get('idpedidocompra');
            $presupuesto->observacion=$request->get('observacion');
            $presupuesto->montoiva10=$request->get('montoiva10');
            $presupuesto->montoiva5=$request->get('montoiva5');
            $presupuesto->montogravada10=$request->get('montogravada10');
            $presupuesto->montogravada5=$request->get('montogravada5');
            $presupuesto->montoexenta=$request->get('montoexenta');
            $presupuesto->montopresupuesto_compra=$request->get('montopresupuesto_compra');
            // Convertir la fecha del formato d-m-Y al formato Y-m-d
            $presupuesto->fechavalidez = $request->get('fechavalidez');
            //$presupuesto->fechavalidez=$request->get('fechavalidez');                   
            $mytime = Carbon::now('America/Asuncion');
            $presupuesto->fecha = now();
            //return dd($presupuesto);
            $presupuesto->estado = 'Pendiente';
            $presupuesto->save();

            $idproducto = $request->get('idproducto');
            $cantidad = $request->get('cantidad');
            $precio = $request->get('precio_compra');
            

            //  select  al producto con inner join de tipo impuesto de lo que te trae tu $idproducto[$cont]
            $productos = DB::table('productos as prod')
            ->join('tipo_impuesto as ti','prod.idtipoimpuesto','=','ti.idtipoimpuesto')
            ->select('prod.idproducto','ti.porcentaje')
            ->whereIn('prod.idproducto',$idproducto)
            ->get();

            
            $prod=0;
            $cont = 0;
            $items = 1;
            $sumiva10=0;
            $sumiva5=0;
            $sumgravada10=0;
            $sumgravada5=0;
            $sumexenta=0;
            $summontoitems=0;

            while ($cont < count($idproducto)) {
                $prod= $productos->where('idproducto','=',$idproducto[$cont])->first();
                //Return dd($prod);
                //  calculo de los demas campos que está en el notepad
                $porcentaje = $prod->porcentaje;
                $montoitems=0;
                $montoitems= $cantidad[$cont]*$precio[$cont];
                
                //Return dd($porcentaje);
                
                if ($porcentaje==10){
                    // Impuesto IVA 10

                    $imp=((100+$porcentaje)/$porcentaje);


                    // Monto Gravado del 10 % y Exento
                    $gravada5=0;
                    $m_gravada10=$montoitems;
                    $exenta=0; 


                    // IVA 10 %
                    $iva10=round($m_gravada10/$imp);
                    $iva5=0;

                    $gravada10 = $m_gravada10 - $iva10;

                    // total
                    $total= $gravada10 + $gravada5 + $exenta + $iva10 + $iva5;

                }else{
                    if ($porcentaje==5) {
                        // Impuesto IVA 5
                        $imp=((100+$porcentaje)/$porcentaje);

                        // Monto Gravado del 5 % y Exento
                        $m_gravada5=$montoitems; 
                        $gravada10=0;
                        $exenta=round((($montoitems) ) - $m_gravada5); 

                        // IVA 5 %
                        $iva10=0;
                        $iva5=round($m_gravada5/$imp);

                        $gravada5 = $m_gravada5 - $iva5;

                        // total
                        $total= $gravada10 + $gravada5 + $exenta + $iva10 + $iva5;

                    }else{
                        if ($porcentaje==0){
                            // Exento
                            
                            // Monto Gravado y Exento
                            $gravada10= 0;
                            $gravada5= 0;
                            $exenta= $montoitems;

                            //iva sin IVA                                 
                            $iva10=0;
                            $iva5=0;

                            //total
                            $total= $gravada10 + $gravada5 + $exenta + $iva10 + $iva5;

                        }
                    }

                }

                    $detalle = new PresupuestosComprasDetalle();
                    $detalle->idpresupuestocompra= $presupuesto->idpresupuestocompra;
                    $detalle->idproducto= $idproducto[$cont];
                    $detalle->cantidad= $cantidad[$cont];
                    $detalle->precio= $precio[$cont];
                    $detalle->items= $items;
                    $detalle->iva10= $iva10;
                    $detalle->iva5= $iva5;
                    $detalle->gravada10= $gravada10;
                    $detalle->gravada5= $gravada5;
                    $detalle->exenta= $exenta;
                    $detalle->montoitems= $montoitems;


                    $detalle->save();

                    $cont=$cont+1;
                    $items++;

                    $sumiva10= $sumiva10 + $iva10;
                    $sumiva5= $sumiva5 + $iva5;
                    $sumgravada10= $sumgravada10 + $gravada10;
                    $sumgravada5= $sumgravada5 + $gravada5;
                    $sumexenta= $sumexenta + $exenta;
                    $summontoitems= $summontoitems + $montoitems;
            }

            $udpcabecera=DB::table('presupuestos_compras')
            ->where('idpresupuestocompra','=',$presupuesto->idpresupuestocompra)
            ->update(['montoiva10'=>$sumiva10,
                        'montoiva5'=>$sumiva5,
                        'montogravada10'=>$sumgravada10,
                        'montogravada5'=>$sumgravada5,
                        'montoexenta'=>$sumexenta,
                        'montopresupuesto_compra'=>$summontoitems]);

            DB::table('pedidos_compras')
                ->where('idpedidocompra', $presupuesto->idpedidocompra)
                ->update(['estado' => 'Realizado']);
            
        DB::commit();
        return Redirect::to('compras/presupuesto')->with('success', 'Operacion exitosa.');
        
    } catch (\Throwable $e) {
        DB::rollback();
        return Redirect::back()
            ->withInput()
            ->withErrors(['presupuesto' => 'No se pudo registrar el presupuesto. Verifique los datos e intente nuevamente.']);
    }

}

public function show($id)
{
    $presupuesto=DB::table('presupuestos_compras as pc')
        ->join('sucursales as s', 'pc.idsucursal', '=', 's.idsucursal')
        ->join('proveedores as p', 'pc.idproveedor', '=', 'p.idproveedor')
        ->join('users as u', 'pc.idusuario', '=', 'u.id')
        ->leftJoin('pedidos_compras as pedido', 'pc.idpedidocompra', '=', 'pedido.idpedidocompra') //para estirar la obs del pedido
        ->select('pc.idpresupuestocompra', 's.idsucursal','s.descripcion', 'p.idproveedor','p.razonsocial', 'p.ruc', 'pc.idpedidocompra', 'pc.fecha','pc.fechavalidez','pedido.observacion as observacion_pedido','u.name as usuario')
       ->where('pc.idpresupuestocompra','=',$id)
       ->orderBy('pc.idpresupuestocompra','desc')           
       ->first();

       //dd($presupuesto);
    $detalles=DB::table('presupuestos_compras_detalle as d')
       ->join('productos as p','d.idproducto','=','p.idproducto')
       ->select('p.descripcion as producto','d.cantidad','d.precio','d.iva10','d.iva5','d.gravada10','d.gravada5','d.exenta','d.montoitems')
       ->where('d.idpresupuestocompra','=',$id) 
       ->get();

       
    return view("compras.presupuesto.show",["presupuesto"=>$presupuesto,"detalles"=>$detalles]);
}
        
        public function destroy($id)
        {
            //PresupuestosComprasDetalle::where('idpresupuestocompra', $id)->delete();
                try {
                    $presupuesto = PresupuestosCompras::findOrFail($id);
                    $presupuesto->estado = 'Cancelado';
                    $presupuesto->save();
        
                    return Redirect::to('compras/presupuesto')->with('success', 'Presupuesto cancelado correctamente.');
                } catch (Exception $e) {
                    return Redirect::to('compras/presupuesto')->with('error', 'Error al cancelar el presupuesto.');
                }
            }
        
        public function insertar_pedidos(Request $request)
        {
            $idpedidocompra = $request->get('numero_pedido');
            $idproveedor = $request->get('proveedor');
            $fechavalidez = $request->get('fechavalidez');
            if (!$fechavalidez || $fechavalidez < now()->toDateString()) {
                return back()->with('error', 'La fecha de validez debe ser igual o posterior a la fecha actual.');
            }
// Validar que el pedido y proveedor hayan sido seleccionados
            if (empty($idpedidocompra)) {
                return back()->with('error', 'Debe seleccionar un número de pedido.');
            }

            if (empty($idproveedor)) {
                return back()->with('error', 'Debe seleccionar un proveedor.');
            }

            // Obtener los datos del pedido
            $pedidos=DB::table('pedidos_compras as pc')        
            ->select('pc.idpedidocompra','pc.idsucursal','pc.observacion','pc.estado')
            ->where('pc.idpedidocompra','=',$idpedidocompra)
            ->where('pc.idsucursal', '=', Auth::user()->trabaja_sucursal)
            ->where('pc.estado', '=', 'Pendiente')
            ->first();

             // Obtener los datos del proveedor
            $proveedores=DB::table('proveedores as prov')        
            ->select('prov.idproveedor','prov.razonsocial','prov.ruc')
            ->where('prov.idproveedor','=',$idproveedor)             
            ->first();

            // Verificar si se encontraron los datos del pedido y proveedor
            if (!$pedidos || !$proveedores) {
                return back()->with('error', 'Pedido pendiente o proveedor no encontrado.');
            }

            //return dd($idpedidocompra,$proveedores);

            $userId = Auth::id();
            $suc = Auth::user()->trabaja_sucursal;

            // Insertar cabecera del presupuesto
            $inscab = PresupuestosCompras::insertGetId([
                'idpedidocompra' => $pedidos->idpedidocompra,
                'idproveedor' => $proveedores->idproveedor,
                'idsucursal' => $suc,
                'idusuario' => $userId,
                'observacion' => $pedidos->observacion,
                'fecha' => now(),
                'fechavalidez' => $fechavalidez,
                'montoiva10' => 0,
                'montoiva5' => 0,
                'montogravada10' => 0,
                'montogravada5' => 0,
                'montoexenta' => 0,
                'montopresupuesto_compra' => 0,
                'estado' => 'Pendiente',
            ]);

            $idcabpresupuesto = $inscab;

            // vas a traer mediante un get() el detalle de los pedidos 
            $pedidos_compras_detalle=DB::table('pedidos_compras_detalle as pcd')        
            ->select('pcd.idpedidocompra_detalle', 'pcd.idpedidocompra', 'pcd.items','pcd.idproducto','pcd.cantidad')
            ->where('pcd.idpedidocompra', '=', $idpedidocompra)
            ->get();

            // Insertar los detalles en presupuestos_compras_detalle
            foreach ($pedidos_compras_detalle as $detalle) {
            DB::table('presupuestos_compras_detalle')->insert([
                'idpresupuestocompra' => $idcabpresupuesto,
                'items' => $detalle->items,
                'idproducto' => $detalle->idproducto,
                'cantidad' => $detalle->cantidad,
                'precio' => 0 // Inicializar con precio 0
            ]);
        }
            // Redirigir al formulario de edición del presupuesto
            return Redirect::to('compras/presupuesto/' . $idcabpresupuesto . '/edit');
        }

        public function edit($id)
{
    $presupuesto = PresupuestosCompras::join('sucursales as s', 'presupuestos_compras.idsucursal', '=', 's.idsucursal')
        ->join('proveedores as p', 'presupuestos_compras.idproveedor', '=', 'p.idproveedor')
        ->join('users as u', 'presupuestos_compras.idusuario', '=', 'u.id')
        ->select('presupuestos_compras.idpresupuestocompra', 's.idsucursal', 's.descripcion', 'p.idproveedor', 'p.razonsocial', 'p.ruc', 'presupuestos_compras.idpedidocompra', 'presupuestos_compras.fecha', 'presupuestos_compras.observacion', 'presupuestos_compras.estado', 'presupuestos_compras.fechavalidez', 'u.name as usuario')
        ->where('presupuestos_compras.idpresupuestocompra', '=', $id)
        ->orderBy('presupuestos_compras.idpresupuestocompra', 'desc')
        ->first();

    $detalles = PresupuestosComprasDetalle::join('productos as pr', 'presupuestos_compras_detalle.idproducto', '=', 'pr.idproducto')
        ->join('marcas as m', 'pr.idmarca', '=', 'm.idmarca')
        ->select('presupuestos_compras_detalle.idpresupuestocompra_detalle', 'pr.idproducto', 'pr.descripcion as producto', 'presupuestos_compras_detalle.cantidad', 'presupuestos_compras_detalle.precio', 'presupuestos_compras_detalle.items','m.descripcion AS marcas')
        ->where('presupuestos_compras_detalle.idpresupuestocompra', '=', $id)
        ->get();

    return view("compras.presupuesto.edit", ["presupuesto" => $presupuesto, "detalles" => $detalles]);
}

public function update(Request $request, $id)
    {
        $idpresupuestocompra_detalle = $request->get('idpresupuestocompra_detalle');
        $cantidad = $request->get('cantidad');
        $precio = $request->get('precio');
        $idproducto = $request->get('idproducto');

        //return dd(['a'=>$cantidad,'b'=>$precio,'c'=>$idproducto, 'd'=>$idpresupuestocompra]);
        
        $productos = DB::table('productos as prod')
        ->join('tipo_impuesto as ti','prod.idtipoimpuesto','=','ti.idtipoimpuesto')
        ->select('prod.idproducto','ti.porcentaje')
        ->whereIn('prod.idproducto',$idproducto)
        ->get();
                
        $prod=0;
        $cont = 0;
        $items = 1;
        $sumiva10=0;
        $sumiva5=0;
        $sumgravada10=0;
        $sumgravada5=0;
        $sumexenta=0;
        $summontoitems=0;

        foreach ($idpresupuestocompra_detalle as $key => $value) {
            
            $prod= $productos->where('idproducto', '=' ,$idproducto[$value])->first();
            $porcentaje = $prod->porcentaje;
            $montoitems=0;
            $montoitems= $cantidad[$value]*$precio[$value];
                        
            if ($porcentaje==10){
                // Impuesto IVA 10

                $imp=((100+$porcentaje)/$porcentaje);


                // Monto Gravado del 10 % y Exento
                $gravada5=0;
                $m_gravada10=$montoitems;
                $exenta=0; 


                // IVA 10 %
                $iva10=round($m_gravada10/$imp);
                $iva5=0;

                $gravada10 = $m_gravada10 - $iva10;

                // total
                $total= $gravada10 + $gravada5 + $exenta + $iva10 + $iva5;

            }else{
                if ($porcentaje==5) {
                    // Impuesto IVA 5
                    $imp=((100+$porcentaje)/$porcentaje);

                    // Monto Gravado del 5 % y Exento
                    $m_gravada5=$montoitems; 
                    $gravada10=0;
                    $exenta=round((($montoitems) ) - $m_gravada5); 

                    // IVA 5 %
                    $iva10=0;
                    $iva5=round($m_gravada5/$imp);

                    $gravada5 = $m_gravada5 - $iva5;

                    // total
                    $total= $gravada10 + $gravada5 + $exenta + $iva10 + $iva5;

                }else{
                    if ($porcentaje==0){
                        // Exento
                        
                        // Monto Gravado y Exento
                        $gravada10= 0;
                        $gravada5= 0;
                        $exenta= $montoitems;

                        //iva sin IVA                                 
                        $iva10=0;
                        $iva5=0;

                        //total
                        $total= $gravada10 + $gravada5 + $exenta + $iva10 + $iva5;

                    }
                }

            }
            $udpdetalle=DB::table('presupuestos_compras_detalle')
            ->where('idpresupuestocompra_detalle','=',$value)
            ->update(['cantidad'=>$cantidad[$value],
                    'precio'=>$precio[$value],
                    'iva10'=>$iva10,
                    'iva5'=>$iva5,
                    'gravada10'=>$gravada10,
                    'gravada5'=>$gravada5,
                    'exenta'=>$exenta,
                    'montoitems'=>$montoitems]);

                
                $cont=$cont+1;
                $items++;

                $sumiva10= $sumiva10 + $iva10;
                $sumiva5= $sumiva5 + $iva5;
                $sumgravada10= $sumgravada10 + $gravada10;
                $sumgravada5= $sumgravada5 + $gravada5;
                $sumexenta= $sumexenta + $exenta;
                $summontoitems= $summontoitems + $montoitems;
        } //fin foreach

        // Validación adicional en el lado del servidor
        $request->validate([
            'precio.*' => 'required|numeric|min:1', // Asegura que todos los precios sean numéricos y mayores a 0
        ]);

        //poner para actualizar el estado del pedido
        $udpcabecera=DB::table('presupuestos_compras')
        ->where('idpresupuestocompra','=',$id)
        ->update(['montoiva10'=>$sumiva10,
                    'montoiva5'=>$sumiva5,
                    'montogravada10'=>$sumgravada10,
                    'montogravada5'=>$sumgravada5,
                    'montoexenta'=>$sumexenta,
                    'montopresupuesto_compra'=>$summontoitems,
                    'observacion' => $request->get('observacion'), // Agrega esta línea para actualizar el campo 'obs'
                    'estado' => 'Pendiente', // Cambiar el estado a "Pendiente"
                ]);
                    
        // Obtener el idpedido asociado a la cabecera presupuestocompra
        $idpedido = DB::table('presupuestos_compras')
        ->where('idpresupuestocompra', '=', $id)
        ->value('idpedidocompra');

        // Actualizar el estado en la tabla pedido_compra
        $udpPedidoEstado = DB::table('pedidos_compras')
        ->where('idpedidocompra', '=', $idpedido)
        ->update(['estado' => 'Realizado']);

        return Redirect::to('compras/presupuesto/'.$id);

    }
        
 /* public function aceptar(Presupuesto $presupuesto)
        {
            try {
                DB::beginTransaction();

                if ($presupuesto->estado != 'Pendiente') {
                    throw new Exception('El presupuesto no está en estado Pendiente');
                }

                $presupuesto->estado = 'Aceptado';
                $presupuesto->save();

                // Cambiar estado del pedido a Realizado

                $pedido = Pedido::find($presupuesto->idpedidocompra);
                $pedido->estado = 'Realizado';
                $pedido->save();

                DB::commit();

                return Redirect::to('presupuestos/presupuesto');
            } catch (Exception $e) {
                DB::rollback();

                return Redirect::back()->withErrors(['error' => $e->getMessage()]);
            }
        }

        public function cancelar(Presupuesto $presupuesto)
        {
            try {
                DB::beginTransaction();

                $presupuesto->estado = 'Cancelado';
                $presupuesto->save();

                DB::commit();

                return Redirect::to('presupuestos/presupuesto');
            } catch (Exception $e) {
                DB::rollback();

                return Redirect::back()->withErrors(['error' => $e->getMessage()]);
            }
        }

       /* public function createFromPedido(Pedido $pedido)
        {
            $presupuesto = new Presupuesto();

            $presupuesto->idsucursal = $pedido->idsucursal;
            $presupuesto->idusuario = $pedido->idusuario;
            $presupuesto->idpedidocompra = $pedido->idpedidocompra;
            $presupuesto->idproveedor = $pedido->idproveedor;
            $presupuesto->fecha = $pedido->fecha;
            $presupuesto->observacion = $pedido->observacion;
            $presupuesto->estado = 'Pendiente';
            $presupuesto->fechavalidez = date('Y-m-d', strtotime('+30 days'));

            // Agregar detalles del presupuesto a pprodir del pedido

            foreach ($pedido->detallePedido as $detalle) {
                $presupuesto_detalle = new PresupuestoCompraDetalle();

                $presupuesto_detalle->idproducto = $detalle->idproducto;
                $presupuesto_detalle->cantidad = $detalle->cantidad;
                $presupuesto_detalle->precio = $detalle->precio;
                $presupuesto_detalle->items = $detalle->items;

                $presupuesto->detalle()->save($presupuesto_detalle);
            }

            $presupuesto->save();

            return Redirect::to('presupuestos/presupuesto/' . $presupuesto->idpresupuestocompra);
        }*/
    }
