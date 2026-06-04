<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\OrdenComprasFormRequest;
use App\Models\OrdenCompras;
use App\Models\OrdenComprasDetalle;
use App\Models\PresupuestosCompras;
use App\Models\PresupuestosComprasDetalle;
use App\Models\Productos;
use App\Models\Proveedores;
use App\Models\Sucursales;
use App\Models\Depositos;
use DB;
use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;

class OrdenComprasController extends Controller
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

        $queryBuilder = DB::table('orden_compras as oc')
                            ->join('sucursales as s', 'oc.idsucursal', '=', 's.idsucursal')
                            ->join('depositos as dep', 'oc.iddeposito', '=', 'dep.iddeposito')
                            ->join('proveedores as p', 'oc.idproveedor', '=', 'p.idproveedor')
                            ->join('users as u', 'oc.idusuario', '=', 'u.id')
                            ->leftJoin('presupuestos_compras as pre', 'oc.idpresupuestocompra', '=', 'pre.idpresupuestocompra')
                            ->select(
                                'oc.idordencompra', 
                                's.idsucursal',
                                's.descripcion as sucursal_descripcion',
                                'dep.iddeposito',
                                'dep.descripcion as deposito_descripcion',  
                                'p.idproveedor',
                                'p.razonsocial', 
                                'p.ruc',
                                'p.direccion', 
                                'pre.idpresupuestocompra',
                                'pre.observacion as presupuesto_observacion',
                                'oc.fecha',
                                'oc.montoiva10', 
                                'oc.montoiva5', 
                                'oc.montogravada10', 
                                'oc.montogravada5', 
                                'oc.montoexenta',
                                'oc.monto_orden_compra',
                                'oc.observacion as orden_observacion',
                                'u.name as usuario',
                                'oc.estado'
                            );

        // Aplicar condiciones WHERE según los términos de búsqueda
        if ($query) {
            $queryBuilder->where('oc.idordencompra', 'LIKE', '%' . $query . '%');
        }
    
        if ($query2) {
            $queryBuilder->where('oc.fecha', 'LIKE', '%' . $query2 . '%');
        }

        if ($query3) {
            $queryBuilder->where('s.descripcion', 'LIKE', '%' . $query3 . '%');
        }

        if ($query4) {
            $queryBuilder->where('p.razonsocial', 'LIKE', '%' . $query4 . '%');
        }
    
        if ($query5) {
            $queryBuilder->where('pre.idpresupuestocompra', 'LIKE', '%' . $query5 . '%');
        }

        if ($query6) {
            $queryBuilder->where('p.ruc', 'LIKE', '%' . $query6 . '%');
        }

        if ($query7) {
            $queryBuilder->where('oc.estado', 'LIKE', '%' . $query7 . '%');
        }                    

        $orden_compras = $queryBuilder->orderBy('oc.idordencompra', 'desc')
        ->groupBy(
            'oc.idordencompra', 
            's.idsucursal',
            's.descripcion',
            'dep.iddeposito',
            'dep.descripcion',  
            'p.idproveedor', 
            'p.razonsocial', 
            'p.ruc',
            'p.direccion',
            'pre.idpresupuestocompra',
            'pre.observacion',
            'oc.fecha', 
            'oc.montoiva10', 
            'oc.montoiva5', 
            'oc.montogravada10', 
            'oc.montogravada5', 
            'oc.montoexenta',
            'oc.monto_orden_compra',
            'oc.observacion',
            'u.name',
            'oc.estado'
        )
        ->paginate(7); // Paginar directamente

         // Obtener total de registros (con los filtros aplicados)
         $total = $orden_compras->total();

         return view('compras.orden.index', [
             "orden_compras" => $orden_compras,
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
        $proveedores=DB::table('proveedores as p')        
        ->select('p.idproveedor', 'p.razonsocial', 'p.ruc','p.direccion')        
        ->get();
        $sucursales=DB::table('sucursales as s')
        ->select('s.idsucursal','s.descripcion')
        ->where('s.idsucursal', '=', $suc)
        ->first();
        $presupuestos=DB::table('presupuestos_compras as pre') 
        ->join('sucursales as s', 'pre.idsucursal', '=', 's.idsucursal') 
        ->join('proveedores as p', 'pre.idproveedor', '=', 'p.idproveedor')  
        ->select('pre.idpresupuestocompra','s.idsucursal','s.descripcion','p.idproveedor','p.razonsocial','p.ruc','p.direccion','pre.fecha','pre.estado') 
        ->where('pre.idsucursal', '=', $suc)
        ->where('pre.estado', '=', 'Pendiente')
        ->get();        
        
        // Obtener depósitos relacionados a la sucursal seleccionada
        $sucursales = Sucursales::find($suc);
        $depositos = $sucursales->depositos()->select('iddeposito', 'descripcion')->get();
            
        // Por defecto, seleccionamos el primer depósito
        $primerDeposito = $depositos->isNotEmpty() ? $depositos->first()->iddeposito : null;  
        
        $productos = DB::table('productos as prod')
        ->select(DB::raw('CONCAT(prod.codigo, " " ,prod.descripcion) AS producto'),'prod.idproducto')
        ->where('prod.estado','=','Activo')
        ->get();
        return view("compras.orden.create",["fecha"=>$fecha,"proveedores"=>$proveedores,"sucursales"=>$sucursales,"depositos"=>$depositos,"productos"=>$productos,"presupuestos"=>$presupuestos]);
    }

    public function store (OrdenComprasFormRequest $request)
    {
        if (!$request->input('ruc')) {
            return redirect()->back()->withErrors(['ruc' => 'Debe seleccionar un proveedor con un RUC válido.']);
        }
        
        try {
            DB::beginTransaction();
                $orden=new OrdenCompras;
                $orden->idproveedor=$request->get('idproveedor');
                $orden->ruc=$request->get('ruc');
                $orden->direccion=$request->get('direccion');
                $orden->idsucursal=Auth::user()->trabaja_sucursal; 
                $orden->idusuario=Auth::id();
                $orden->iddeposito=$request->get('iddeposito');                 
                $orden->idpresupuestocompra=$request->get('idpresupuestocompra');
                $orden->observacion=$request->get('observacion');
                $orden->montoiva10=$request->get('montoiva10');
                $orden->montoiva5=$request->get('montoiva5');
                $orden->montogravada10=$request->get('montogravada10');
                $orden->montogravada5=$request->get('montogravada5');
                $orden->montoexenta=$request->get('montoexenta');
                $orden->monto_orden_compra=$request->get('monto_orden_compra');
                                   
                $mytime = Carbon::now('America/Asuncion');
                $orden->fecha=$mytime->toDateTimeString();
                $orden->estado='Pendiente';
               
                $orden->save();

                $idproducto = $request->get('idproducto');
                $cantidad = $request->get('cantidad');
                $precio_compra = $request->get('precio_compra');
                

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
                    $montoitems= $cantidad[$cont]*$precio_compra[$cont];
                    
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

                        $detalle = new OrdenComprasDetalle();
                        $detalle->idordencompra= $orden->idordencompra;
                        $detalle->idproducto= $idproducto[$cont];
                        $detalle->cantidad= $cantidad[$cont];
                        $detalle->precio_compra= $precio_compra[$cont];
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

                $udpcabecera=DB::table('orden_compras')
                ->where('idordencompra','=',$orden->idordencompra)
                ->update(['montoiva10'=>$sumiva10,
                            'montoiva5'=>$sumiva5,
                            'montogravada10'=>$sumgravada10,
                            'montogravada5'=>$sumgravada5,
                            'montoexenta'=>$sumexenta,
                            'monto_orden_compra'=>$summontoitems]);

                
            DB::commit();
            
        } catch (\Throwable $e) {
            DB::rollback();
        }

        return Redirect::to('compras/orden');

    }

    public function show($id)
    {
        $orden=DB::table('orden_compras as oc')
            ->join('sucursales as s', 'oc.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'oc.iddeposito', '=', 'dep.iddeposito')
            ->join('proveedores as p', 'oc.idproveedor', '=', 'p.idproveedor')  
            ->join('users as u', 'oc.idusuario', '=', 'u.id')
            ->leftJoin('presupuestos_compras as pre', 'oc.idpresupuestocompra', '=', 'pre.idpresupuestocompra') //para estirar la obs del presupuesto      
            ->select('oc.idordencompra', 's.idsucursal', 's.descripcion as sucursal', 'dep.iddeposito', 'dep.descripcion as deposito', 'p.idproveedor', 'p.razonsocial as proveedor', 'p.ruc', 'p.direccion', 'oc.idpresupuestocompra', 'oc.fecha', 'oc.montoiva10', 'oc.montoiva5', 'oc.montogravada10', 'oc.montogravada5', 'oc.montoexenta', 'oc.monto_orden_compra', 'oc.observacion', 'oc.estado','u.name as usuario','pre.observacion as observacion_presupuesto')
           ->where('oc.idordencompra','=',$id)
           ->orderBy('oc.idordencompra','desc')           
           ->first();
           //dd($orden);

        $detalles=DB::table('orden_detalle as d')
           ->join('productos as prod','d.idproducto','=','prod.idproducto')
           ->select('prod.descripcion as producto','d.cantidad','d.precio_compra','d.iva10','d.iva5','d.gravada10','d.gravada5','d.exenta','d.montoitems')
           ->where('d.idordencompra','=',$id) 
           ->get();

        return view("compras.orden.show",["orden"=>$orden,"detalles"=>$detalles]);
    }

    public function destroy($id)
    {
        //OrdenComprasDetalle::where('idordencompra', $id)->delete();
        try {
            $orden = OrdenCompras::findOrFail($id);
            $orden->estado = 'Cancelado';
            $orden->save();

            return Redirect::to('compras/orden')->with('success', 'Orden cancelada correctamente.');
        } catch (\Throwable $e) {
            return Redirect::to('compras/orden')->with('error', 'Error al cancelar la orden.');
        }
    }

    public function insertar_presupuestos (Request $request)
    {
        $idpresupuestocompra = $request->get('numero_presupuesto');
        $iddeposito = $request->get('iddeposito');

        if (empty($idpresupuestocompra)) {
            return back()->with('error', 'Debe seleccionar un presupuesto.');
        }

        if (empty($iddeposito)) {
            return back()->with('error', 'Debe seleccionar un deposito.');
        }

        $presupuestos = DB::table('presupuestos_compras as p')
            ->select('p.idpresupuestocompra', 'p.idsucursal', 'p.observacion', 'p.idproveedor', 'p.estado')
            ->where('p.idpresupuestocompra', '=', $idpresupuestocompra)
            ->where('p.idsucursal', '=', Auth::user()->trabaja_sucursal)
            ->where('p.estado', '=', 'Pendiente')
            ->first();

        $deposito = DB::table('depositos')->where('iddeposito', $iddeposito)->first();

        if (!$presupuestos || !$deposito) {
            return back()->with('error', 'Presupuesto pendiente o deposito no encontrado.');
        }

        $proveedores = DB::table('proveedores as p')
            ->select('p.idproveedor', 'p.razonsocial', 'p.ruc', 'p.direccion')
            ->where('p.idproveedor', '=', $presupuestos->idproveedor)
            ->first();

        if (!$proveedores) {
            return back()->with('error', 'Proveedor no encontrado.');
        }

        try {
            DB::beginTransaction();

            $userId = Auth::id();
            $suc = Auth::user()->trabaja_sucursal;
            $inscab = DB::table('orden_compras')->insertGetId([
                'idpresupuestocompra' => $presupuestos->idpresupuestocompra,
                'idproveedor' => $proveedores->idproveedor,
                'ruc' => $proveedores->ruc,
                'direccion' => $proveedores->direccion,
                'idsucursal' => $suc,
                'idusuario' => $userId,
                'observacion' => $presupuestos->observacion,
                'iddeposito' => $iddeposito,
                'fecha' => now(),
                'estado' => 'Pendiente',
                'montoiva10' => 0,
                'montoiva5' => 0,
                'montogravada10' => 0,
                'montogravada5' => 0,
                'montoexenta' => 0,
                'monto_orden_compra' => 0,
            ]);

            $presupuestos_compras_detalle = DB::table('presupuestos_compras_detalle as pcd')
                ->select('pcd.idpresupuestocompra_detalle', 'pcd.idpresupuestocompra', 'pcd.items', 'pcd.idproducto', 'pcd.cantidad', 'pcd.precio')
                ->where('pcd.idpresupuestocompra', '=', $idpresupuestocompra)
                ->get();

            foreach ($presupuestos_compras_detalle as $detalle) {
                DB::table('orden_detalle')->insert([
                    'idordencompra' => $inscab,
                    'items' => $detalle->items,
                    'idproducto' => $detalle->idproducto,
                    'cantidad' => $detalle->cantidad,
                    'precio_compra' => $detalle->precio,
                    'montoitems' => 0,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'No se pudo generar la orden desde el presupuesto.');
        }

        return Redirect::to('compras/orden/'.$inscab.'/edit');

    }
    public function edit($id)
    {
        $orden=DB::table('orden_compras as oc')
            ->join('sucursales as s', 'oc.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'oc.iddeposito', '=', 'dep.iddeposito')
            ->join('proveedores as p', 'oc.idproveedor', '=', 'p.idproveedor')
            ->join('users as u', 'oc.idusuario', '=', 'u.id')
            ->select('oc.idordencompra', 's.idsucursal', 's.descripcion as sucursal', 'dep.iddeposito', 'dep.descripcion as deposito', 'p.idproveedor', 'p.razonsocial as proveedor', 'p.ruc', 'p.direccion', 'oc.idpresupuestocompra', 'oc.fecha', 'oc.montoiva10', 'oc.montoiva5', 'oc.montogravada10', 'oc.montogravada5', 'oc.montoexenta', 'oc.monto_orden_compra', 'oc.observacion', 'oc.estado','u.name as usuario')
           ->where('oc.idordencompra','=',$id)
           ->orderBy('oc.idordencompra','desc')           
           ->first();

        $detalles=DB::table('orden_detalle as d')
           ->join('productos as prod','d.idproducto','=','prod.idproducto')
           ->select('d.idorden_detalle','prod.idproducto','prod.descripcion as producto','d.cantidad','d.precio_compra','d.iva10','d.iva5','d.gravada10','d.gravada5','d.exenta','d.montoitems')
           ->where('d.idordencompra','=',$id) 
           ->get();

           return view("compras.orden.edit",["orden"=>$orden,"detalles"=>$detalles]);
    }

     public function update(Request $request, $id)
    {
        $idorden_detalle = $request->get('idorden_detalle');
        $cantidad = $request->get('cantidad');
        $precio_compra = $request->get('precio_compra');
        $idproducto = $request->get('idproducto');

        // return dd(['a'=>$cantidad,'b'=>$precio_compra,'c'=>$idproducto, 'd'=>$idpresupuestoc_detalle]);
        
        $productos = DB::table('productos as prod')
        ->join('tipo_impuesto as ti','prod.idtipoimpuesto','=','ti.idtipoimpuesto')
        ->select('prod.idproducto','ti.porcentaje')
        ->whereIn('prod.idproducto',$idproducto)
        ->get();
        
        // return dd(['a'=>$producto,'b'=>$precio_compra,'c'=>$idproducto, 'd'=>$idpresupuestoc_detalle]);
                
        $prod=0;
        $cont = 0;
        $items = 1;
        $sumiva10=0;
        $sumiva5=0;
        $sumgravada10=0;
        $sumgravada5=0;
        $sumexenta=0;
        $summontoitems=0;

        foreach ($idorden_detalle as $key => $value) {
            
            $prod= $productos->where('idproducto', '=' ,$idproducto[$value])->first();
            //  calculo de los demas campos que está en el notepad
            $porcentaje = $prod->porcentaje;
            $montoitems=0;
            $montoitems= $cantidad[$value]*$precio_compra[$value];
            // return dd(['a'=>$cantidad[$value],'b'=>$precio_compra[$value],'c'=>$montoitems]);
                        
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
            $udpdetalle=DB::table('orden_detalle')
            ->where('idorden_detalle','=',$value)
            ->update([
                    'cantidad'=>$cantidad[$value],
                    'precio_compra'=>$precio_compra[$value],
                    'iva10'=>$iva10,
                    'iva5'=>$iva5,
                    'gravada10'=>$gravada10,
                    'gravada5'=>$gravada5,
                    'exenta'=>$exenta,
                    'montoitems'=>$montoitems
                ]);

                
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
            'precio_compra.*' => 'required|numeric|min:1', // Asegura que todos los precios_compra sean numéricos y mayores a 0
        ]);
        
        $udpcabecera=DB::table('orden_compras')
        ->where('idordencompra','=',$id)
        ->update(['montoiva10'=>$sumiva10,
                    'montoiva5'=>$sumiva5,
                    'montogravada10'=>$sumgravada10,
                    'montogravada5'=>$sumgravada5,
                    'montoexenta'=>$sumexenta,
                    'monto_orden_compra'=>$summontoitems,
                    'observacion' => $request->get('observacion'), // Agrega esta línea para actualizar el campo 'obs'
                    'estado' => 'Pendiente',// Cambiar el estado a "Pendiente"
                ]);
                    
        // Obtener el idpresupuesto_compra asociado a la cabecera orden_compra
        $idpresupuestocompra = DB::table('orden_compras')
        ->where('idordencompra', '=', $id)
        ->value('idpresupuestocompra');

        // Actualizar el estado en la tabla presupuesto_compra
        $udpPresupuestoEstado = DB::table('presupuestos_compras')
        ->where('idpresupuestocompra', '=', $idpresupuestocompra)
        ->update(['estado' => 'Realizado']);

        return Redirect::to('compras/orden/'.$id);

    }
    
}
