<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\NotaCreditoCRequest;
use App\Models\NotaCreditoC;
use App\Models\NotaCreditocDetalle;
use DB;
use App\Models\Stock;
use App\Models\Sucursales;
use Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;

class NotaCreditoCController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        if ($request)
        {
        $query = $request->get('searchText');
        $query2 = $request->get('searchText2');
        $query3 = $request->get('searchText3');
        $query4 = $request->get('searchText4');      
        $query5 = $request->get('searchText5');
        $query6 = $request->get('searchText6');

        $nota_creditoc = DB::table('nota_credito_compra as c')
        ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
        ->join('depositos as dep', 'c.iddeposito', '=', 'dep.iddeposito')
        ->join('proveedores as p', 'c.idproveedor', '=', 'p.idproveedor')   
        ->select('c.idnota_creditoc','c.idcompra', 'c.usuario', 's.idsucursal', 's.descripcion as sucursal', 'dep.iddeposito', 'dep.descripcion as deposito', 'p.idproveedor', 'p.nombre as proveedor', 'p.num_documento', 'c.fecha_registro', 'c.totaliva10', 'c.totaliva5', 'c.totalgravada10', 'c.totalgravada5', 'c.totalexenta', 'c.totalcompra', 'c.timbrado', 'c.condicion', 'c.concepto' , 'c.nro_factura','c.estado','c.fecha_factura','c.fecha_vencimiento')
        ->where('c.idnota_creditoc', 'LIKE', '%'.$query.'%')
        ->Where('p.nombre', 'LIKE', '%'.$query2.'%')
        ->Where('c.fecha_registro', 'LIKE', '%'.$query3.'%')
        ->Where('s.descripcion', 'LIKE', '%'.$query4.'%')
        ->Where('c.nro_factura', 'LIKE', '%'.$query5.'%')        
        ->Where('p.num_documento', 'LIKE', '%'.$query6.'%')
        
        ->orderBy('c.idcompra', 'desc')
        ->paginate(7);

           return view('compras.nota_creditoc.index',["nota_creditoc"=>$nota_creditoc,"searchText"=>$query,"searchText2"=>$query,"searchText3"=>$query,"searchText4"=>$query,"searchText5"=>$query,"searchText6"=>$query6]);
        }
    }

    public function create()
    {
        $suc = Auth::user()->trabaja_sucursal;

        $compras=DB::table('compra as c') 
        ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal') 
        ->join('proveedores as p', 'c.idproveedor', '=', 'p.idproveedor')  
        ->join('depositos as dep', 'c.iddeposito', '=', 'dep.iddeposito') 
        ->select('c.idcompra', 'c.idordencompra', 'c.usuario', 's.idsucursal', 's.descripcion as sucursal', 'dep.iddeposito', 'dep.descripcion as deposito', 'p.idproveedor', 'p.nombre as proveedor', 'p.num_documento', 'c.fecha_registro', 'c.totaliva10', 'c.totaliva5', 'c.totalgravada10', 'c.totalgravada5', 'c.totalexenta', 'c.totalcompra', 'c.timbrado', 'c.condicion', 'c.concepto' , 'c.nro_factura','c.estado','c.fecha_factura','c.fecha_vencimiento') 
        ->where('c.idsucursal', '=', $suc)
        ->where('c.estado', '=', 'R')
        ->get();    

        $proveedores=DB::table('proveedores as p')        
        ->select('p.idproveedor', 'p.nombre', 'p.num_documento','p.direccion')        
        ->get();
        $sucursales=DB::table('sucursales as s')
        ->select('s.idsucursal','s.descripcion')
        ->where('s.idsucursal', '=', $suc)
        ->first();
        
        // Obtener depósitos relacionados a la sucursal seleccionada
        $sucursales = Sucursales::find($suc);
        $depositos = $sucursales->depositos()->select('iddeposito', 'descripcion')->get();
            
        // Por defecto, seleccionamos el primer depósito
        $primerDeposito = $depositos->isNotEmpty() ? $depositos->first()->iddeposito : null;  

        $productos = DB::table('productos as prod')
        ->select(DB::raw('CONCAT(prod.codigo, " " ,prod.nombre) AS producto'),'prod.idproducto')
        ->where('prod.estado','=','Activo')
        ->get();
        return view("compras.nota_creditoc.create",["proveedores"=>$proveedores,"sucursales"=>$sucursales,"depositos"=>$depositos,"productos"=>$productos,"compras"=>$compras]);
    }

    public function store (NotaCreditoCRequest $request)
    {        
        try {
            DB::beginTransaction();
                $nota_creditoc=new NotaCreditoC;
                $nota_creditoc->idproveedor=$request->get('idproveedor');
                $nota_creditoc->ruc=$request->get('ruc');
                $nota_creditoc->idcompra=$request->get('idcompra');
                $nota_creditoc->idsucursal=$request->get('idsucursal'); 
                $nota_creditoc->iddeposito=$request->get('iddeposito');         
                $nota_creditoc->usuario=$request->get('usuario');
                $nota_creditoc->nro_factura=$request->get('nro_factura');
                //$nota_creditoc->condicion=$request->get('condicion');                
                $nota_creditoc->concepto=$request->get('concepto');
                $nota_creditoc->timbrado=$request->get('timbrado');
                $nota_creditoc->totaliva10=$request->get('totaliva10');
                $nota_creditoc->totaliva5=$request->get('totaliva5');
                $nota_creditoc->totalgravada10=$request->get('totalgravada10');
                $nota_creditoc->totalgravada5=$request->get('totalgravada5');
                $nota_creditoc->totalexenta=$request->get('totalexenta');
                $nota_creditoc->totalcompra=$request->get('totalcompra');
                                   
                $mytime = Carbon::now('America/Asuncion');
                $nota_creditoc->fecha_registro=$mytime->toDateTimeString(); 
                $nota_creditoc->fecha_factura=$request->get('fecha_factura');
                $nota_creditoc->fecha_vencimiento = $request->get('fecha_vencimiento');

                $nota_creditoc->estado='R';
               
                $nota_creditoc->save();

                $idproducto = $request->get('idproducto');
                $cantidad = $request->get('cantidad');
                $precio_compra = $request->get('precio_compra');
                

                //  select  al arituculo con inner join de tipo impuesto de lo que te trae tu $idproducto[$cont]
                $productos = DB::table('productos as prod')
                ->join('tipo_impuesto as ti','prod.idtipo_impuesto','=','ti.idtipo_impuesto')
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
                $sumtotalitems=0;

                while ($cont < count($idproducto)) {
                    $prod= $productos->where('idproducto','=',$idproducto[$cont])->first();
                    //Return dd($prod);
                    
                    //  calculo de los demas campos que está en el notepad
                    $porcentaje = $prod->porcentaje;
                    $totalitems=0;
                    $totalitems= $cantidad[$cont]*$precio_compra[$cont];
                    
                    //Return dd($porcentaje);
                    
                    if ($porcentaje==10){
                        // Impuesto IVA 10

                        $imp=((100+$porcentaje)/$porcentaje);


                        // Monto Gravado del 10 % y Exento
                        $gravada5=0;
                        $m_gravada10=$totalitems;
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
                            $m_gravada5=$totalitems; 
                            $gravada10=0;
                            $exenta=round((($totalitems) ) - $m_gravada5); 

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
                                $exenta= $totalitems;

                                //iva sin IVA                                 
                                $iva10=0;
                                $iva5=0;

                                //total
                                $total= $gravada10 + $gravada5 + $exenta + $iva10 + $iva5;

                            }
                        }

                    }

                        $detalle = new NotaCreditoCDetalle();
                        $detalle->idnota_creditoc= $nota_creditoc->idnota_creditoc;
                        $detalle->idproducto= $idproducto[$cont];
                        $detalle->cantidad= $cantidad[$cont];
                        $detalle->precio_compra= $precio_compra[$cont];
                        $detalle->items= $items;
                        $detalle->iva10= $iva10;
                        $detalle->iva5= $iva5;
                        $detalle->gravada10= $gravada10;
                        $detalle->gravada5= $gravada5;
                        $detalle->exenta= $exenta;
                        $detalle->totalitems= $totalitems;
                        
                        $detalle->save();
                        //calcular mi stock

                        $con_stk=DB::select("Select idstock, existencia from stock where idsucursal=".$request->get('idsucursal')." and iddeposito=".$request->get('iddeposito')." and idproducto=".$idproducto[$cont]);
                        
                        $existe=0; $existencia=0;
						foreach ($con_stk as $cst) {
							$existe=1;
							$idstock=$cst->idstock;
							$existencia=$cst->existencia;
                            
						}

						if ($existe == 1) {
                            // Modificar la existencia para una salida
                            if ($existencia >= $cantidad[$cont]) {
                                $salida = DB::update("UPDATE stock SET existencia = existencia - ".$cantidad[$cont]." WHERE idsucursal=".$request->get('idsucursal')." AND iddeposito=".$request->get('iddeposito')." AND idproducto=".$idproducto[$cont]." AND idstock=".$idstock);
                            } else {
                                // No hay suficiente stock para la salida
                                session()->flash('error', 'No hay suficiente stock para este producto.');
                            }
                        } else {
                            // No existe un registro en stock para el producto
                            session()->flash('error', 'No hay stock para este producto en la sucursal y depósito especificados.');
                        }


                        $cont=$cont+1;
                        $items++;

                        $sumiva10= $sumiva10 + $iva10;
                        $sumiva5= $sumiva5 + $iva5;
                        $sumgravada10= $sumgravada10 + $gravada10;
                        $sumgravada5= $sumgravada5 + $gravada5;
                        $sumexenta= $sumexenta + $exenta;
                        $sumtotalitems= $sumtotalitems + $totalitems;
							
				}
                    
            
                $udpcabecera=DB::table('nota_credito_compra')
                ->where('idnota_creditoc','=',$nota_creditoc->idnota_creditoc)
                ->update(['totaliva10'=>$sumiva10,
                            'totaliva5'=>$sumiva5,
                            'totalgravada10'=>$sumgravada10,
                            'totalgravada5'=>$sumgravada5,
                            'totalexenta'=>$sumexenta,
                            'totalcompra'=>$sumtotalitems]); 
                     
                $updateCuentaPagar=DB::table('cuentas_a_pagar')
                    ->where('idcompra', $nota_creditoc->idcompra)
                    ->update(['montoapagar' => DB::raw('GREATEST(0, montoapagar - ' . (int) $sumtotalitems . ')')]);
                     
               
            DB::commit();
            
        } catch (Exception $e) {
            DB::rollback();
            // Imprimir o registrar el mensaje de error para depuración
            Log::error('Error al actualizar la cuenta a pagar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un problema al actualizar la cuenta a pagar');
        }

        return Redirect::to('compras/nota_creditoc');

    }
   

    public function show($id)
    {
        $nota_creditoc=DB::table('nota_credito_compra as c')
            ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'c.iddeposito', '=', 'dep.iddeposito')
            ->join('proveedores as p', 'c.idproveedor', '=', 'p.idproveedor')        
            ->select('c.idnota_creditoc', 'c.usuario', 's.idsucursal', 's.descripcion as sucursal', 'dep.iddeposito', 'dep.descripcion as deposito', 'p.idproveedor', 'p.nombre as proveedor', 'p.num_documento','c.idcompra', 'c.fecha_registro', 'c.totaliva10', 'c.totaliva5', 'c.totalgravada10', 'c.totalgravada5', 'c.totalexenta', 'c.totalcompra', 'c.timbrado', 'c.condicion', 'c.concepto' , 'c.nro_factura','c.estado','c.fecha_factura','c.fecha_vencimiento')
           ->where('c.idnota_creditoc','=',$id)
           ->orderBy('c.idnota_creditoc','desc')
           //->groupBy('c.idnota_creditoc', 'c.usuario', 's.idsucursal', 's.descripcion', 'dep.iddeposito', 'dep.descripcion', 'p.idproveedor', 'p.nombre', 'p.num_documento','c.idordencompra', 'c.fecha_registro', 'c.totaliva10', 'c.totaliva5', 'c.totalgravada10', 'c.totalgravada5', 'c.totalexenta', 'c.totalcompra', 'c.timbrado', 'c.condicion', 'c.concepto' , 'c.nro_factura','c.estado','c.fecha_factura','c.fecha_vencimiento')
           ->first();

        $detalles=DB::table('nota_credito_compra_detalle as d')
           ->join('productos as a','d.idproducto','=','a.idproducto')
           ->select('a.nombre as producto','d.cantidad','d.precio_compra','d.iva10','d.iva5','d.gravada10','d.gravada5','d.exenta','d.totalitems')
           ->where('d.idnota_creditoc','=',$id) 
           ->get();

        return view("compras.nota_creditoc.show",["nota_creditoc"=>$nota_creditoc,"detalles"=>$detalles]);
    }

    public function destroy($id)
    {
        $nota_creditoc=NotaCreditoC::findOrFail($id);        
        $nota_creditoc->Estado='A';
        $nota_creditoc->update();
        return Redirect::to('compras/nota_creditoc')->with('success', 'Nota de Crédito anulado correctamente.'); 
    }

    public function destroydetalle($id)
    {
       
        $nota_creditoc=NotaCreditocDetalle::findOrFail($id);        
        $idcab=$nota_creditoc->idnota_creditoc;
        $nota_creditoc->delete();

        return Redirect::to('compras/nota_creditoc/'.$idcab.'/edit');        
    }

    public function insertar_facturas (Request $request)
    {
        // Validación de campos
        $validator = Validator::make($request->all(), [                     
            'nro_factura' => 'required',
            'timbrado' => 'required',
            //'condicion' => 'required'            
        ]);

        // Manejo de errores de validación
        if ($validator->fails()) {
            $errors = $validator->errors();
            $error_message = implode('<br>', $errors->all());
        
            return response()->json(['error' => $error_message], 400);
        }
        
        $idcompra = $request->get('numero_factura');    
        $nro_factura = $request->get('nro_factura');        //el nro de nota de crédito
        //$condicion = $request->get('condicion');
        $concepto = $request->get('concepto');
        $timbrado = $request->get('timbrado');
        $fecha_factura = $request->get('fecha_factura');
        //$fecha_vencimiento = $request->get('fecha_vencimiento');
        //return dd( $request->get('numero_factura'));
        // Asigna el resultado de la consulta a la variable $compra
        $compras = DB::table('compra as oc')        
            ->select('oc.idcompra','oc.idsucursal','oc.idproveedor','oc.iddeposito')
            ->where('oc.idcompra','=',$idcompra)
            ->first();
        
        if ($compras) {
            // Traer proveedor por first() para poder capturar su ruc
            $proveedores = DB::table('proveedores as p')
                ->select('p.idproveedor', 'p.nombre', 'p.num_documento', 'p.direccion')
                ->where('p.idproveedor', '=', $compras->idproveedor)
                ->first();
            //return dd($compras);
        } else {
            // Manejar el caso en el que $compras es nulo
            $error_message = "No se encontraron datos para el número de compra proporcionado.";
            // Puedes agregar más información al mensaje de error según tus necesidades.
            return response()->json(['error' => $error_message], 404);
            // Puedes cambiar el código de respuesta y el formato del mensaje según tus requisitos.
                }
            //return dd($compras);
        // Insertar en la tabla nota de credito 
        $user = Auth::user()->name;
        $suc = Auth::user()->trabaja_sucursal;
        $inscab = DB::table('nota_credito_compra')->insertGetId([
            'idcompra' => $compras->idcompra,
            'idproveedor' => $proveedores->idproveedor,
            'ruc' => $proveedores->num_documento,            
            'idsucursal' => $suc,
            'usuario' => $user,            
            'iddeposito' => $compras->iddeposito, //Así estiro mi deposito de mi orden...
            'fecha_registro' => now(),
            'fecha_factura' => $fecha_factura,
            //'fecha_vencimiento' => $fecha_vencimiento,
            'estado' => 'R',  // Establece un valor por defecto
            'nro_factura' => $nro_factura,
            'timbrado' => $timbrado,
            //'condicion' => $condicion,
            'concepto' => $concepto
        ]);
        //return dd( $inscab);
        $idcaborden = $inscab;     
        //dd($idcaborden); 
        // vas a traer mediante un get() el detalle de los presupuestos 
        $compra_detalle=DB::table('compra_detalle as od')        
        ->select('od.idcompra_detalle', 'od.idcompra', 'od.items','od.idproducto','od.cantidad','od.precio_compra')
        ->where('od.idcompra', '=', $idcompra)
        ->get();
        //dd($orden_detalle);
         
        // Antes de la inserción, verifica que $idcaborden existe en la tabla presupuestocompra
        
        // Insertar detalles en la tabla nota_credito_compra_detalle
        foreach ($compra_detalle as $detalle) {
            DB::table('nota_credito_compra_detalle')->insert([
                'idnota_creditoc' => $idcaborden,
                'items' => $detalle->items,
                'idproducto' => $detalle->idproducto,
                'cantidad' => $detalle->cantidad,
                'precio_compra' => $detalle->precio_compra
            ]);
        }
       
        return Redirect::to('compras/nota_creditoc/'.$idcaborden.'/edit');

    }

    public function edit($id)
    {
        $nota_creditoc=DB::table('nota_credito_compra as c')
            ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'c.iddeposito', '=', 'dep.iddeposito')
            ->join('proveedores as p', 'c.idproveedor', '=', 'p.idproveedor')        
            ->select('c.idnota_creditoc', 'c.usuario', 's.idsucursal', 's.descripcion as sucursal', 'dep.iddeposito', 'dep.descripcion as deposito', 'p.idproveedor', 'p.nombre as proveedor', 'p.num_documento','c.idcompra', 'c.fecha_registro', 'c.totaliva10', 'c.totaliva5', 'c.totalgravada10', 'c.totalgravada5', 'c.totalexenta', 'c.totalcompra', 'c.timbrado', 'c.condicion', 'c.concepto' , 'c.nro_factura','c.estado','c.fecha_factura','c.fecha_vencimiento')
           ->where('c.idnota_creditoc','=',$id)
           ->orderBy('c.idnota_creditoc','desc')           
           ->first();

        $detalles=DB::table('nota_credito_compra_detalle as d')
           ->join('productos as a','d.idproducto','=','a.idproducto')
           ->select('idnota_creditoc_detalle','a.idproducto','a.nombre as producto','d.cantidad','d.precio_compra','d.iva10','d.iva5','d.gravada10','d.gravada5','d.exenta','d.totalitems')
           ->where('d.idnota_creditoc','=',$id) 
           ->get();
           return view("compras.nota_creditoc.edit",["nota_creditoc"=>$nota_creditoc,"detalles"=>$detalles]);
    }

    public function update(Request $request, $id)
    {
        // Validación adicional en el lado del servidor
        $request->validate([
            'precio_compra.*' => 'required|numeric|min:1', // Asegura que todos los precios_compra sean numéricos y mayores a 0
        ]);
        $idnota_creditoc_detalle = $request->get('idnota_creditoc_detalle');
        $cantidad = $request->get('cantidad');
        $precio_compra = $request->get('precio_compra');
        $idproducto = $request->get('idproducto');

        // return dd(['a'=>$cantidad,'b'=>$precio_compra,'c'=>$idproducto, 'd'=>$idcompra_detalle]);
        
        $productos = DB::table('productos as prod')
        ->join('tipo_impuesto as ti','prod.idtipo_impuesto','=','ti.idtipo_impuesto')
        ->select('prod.idproducto','ti.porcentaje')
        ->whereIn('prod.idproducto',$idproducto)
        ->get();
        
        // return dd(['a'=>$productos,'b'=>$precio_compra,'c'=>$idproducto, 'd'=>$idpresupuestoc_detalle]);
       // Obtén los valores necesarios de la cabecera de la compra
       $notacreditocCabecera = DB::table('nota_credito_compra')
       ->where('idnota_creditoc', '=', $id)
       ->select('idproveedor', 'idsucursal', 'fecha_vencimiento', 'fecha_factura','iddeposito','idcompra')
       ->first();
       
        $idcompra = $notacreditocCabecera->idcompra;
        $idproveedor = $notacreditocCabecera->idproveedor;
        $idsucursal = $notacreditocCabecera->idsucursal;
        $iddeposito = $notacreditocCabecera->iddeposito;
        $fecha_vencimiento = $notacreditocCabecera->fecha_vencimiento;
        $fecha_factura = $notacreditocCabecera->fecha_factura;
               

        $prod=0;
        $cont = 0;
        $items = 1;
        $sumiva10=0;
        $sumiva5=0;
        $sumgravada10=0;
        $sumgravada5=0;
        $sumexenta=0;
        $sumtotalitems=0;

        foreach ($idnota_creditoc_detalle as $key => $value) {
            
            $prod= $productos->where('idproducto', '=' ,$idproducto[$value])->first();
            // return dd($prod);
            //  calculo de los demas campos que está en el notepad
            $porcentaje = $prod->porcentaje;
            $totalitems=0;
            $totalitems= $cantidad[$value]*$precio_compra[$value];
            // return dd(['a'=>$cantidad[$value],'b'=>$precio_compra[$value],'c'=>$totalitems]);
                        
            if ($porcentaje==10){
                // Impuesto IVA 10

                $imp=((100+$porcentaje)/$porcentaje);


                // Monto Gravado del 10 % y Exento
                $gravada5=0;
                $m_gravada10=$totalitems;
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
                    $m_gravada5=$totalitems; 
                    $gravada10=0;
                    $exenta=round((($totalitems) ) - $m_gravada5); 

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
                        $exenta= $totalitems;

                        //iva sin IVA                                 
                        $iva10=0;
                        $iva5=0;

                        //total
                        $total= $gravada10 + $gravada5 + $exenta + $iva10 + $iva5;

                    }
                }

            }
            $udpdetalle=DB::table('nota_credito_compra_detalle')
            ->where('idnota_creditoc_detalle','=',$value)
            ->update([
                    'cantidad'=>$cantidad[$value],
                    'precio_compra'=>$precio_compra[$value],
                    'iva10'=>$iva10,
                    'iva5'=>$iva5,
                    'gravada10'=>$gravada10,
                    'gravada5'=>$gravada5,
                    'exenta'=>$exenta,
                    'totalitems'=>$totalitems
                ]);

            $con_stk=DB::select("Select idstock, existencia from stock where idsucursal=".$idsucursal." and iddeposito=".$iddeposito." and idproducto=".$idproducto[$value]);
                    
            $existe=0; $existencia=0;
            foreach ($con_stk as $cst) {
                $existe=1;
                $idstock=$cst->idstock;
                $existencia=$cst->existencia;
                
            }
            //return dd($con_stk, $existe, $idstock );

            if ($existe == 1) {
                // Modificar la existencia para una salida
                if ($existencia >= $cantidad[$value]) {
                    $salida = DB::update("UPDATE stock SET existencia = existencia - ".$cantidad[$value]." WHERE idsucursal=".$idsucursal." AND iddeposito=".$iddeposito." AND idproducto=".$idproducto[$value]." AND idstock=".$idstock);
                } else {
                    // No hay suficiente stock para la salida
                    session()->flash('error', 'No hay suficiente stock para este producto.');
                }
            } else {
                // No existe un registro en stock para el producto
                session()->flash('error', 'No hay stock para este producto en la sucursal y depósito especificados.');
            }            
                
            $cont=$cont+1;
            $items++;

            $sumiva10= $sumiva10 + $iva10;
            $sumiva5= $sumiva5 + $iva5;
            $sumgravada10= $sumgravada10 + $gravada10;
            $sumgravada5= $sumgravada5 + $gravada5;
            $sumexenta= $sumexenta + $exenta;
            $sumtotalitems= $sumtotalitems + $totalitems;

        } //fin foreach
                
        $udpcabecera=DB::table('nota_credito_compra')
        ->where('idnota_creditoc','=',$id)
        ->update(['totaliva10'=>$sumiva10,
                    'totaliva5'=>$sumiva5,
                    'totalgravada10'=>$sumgravada10,
                    'totalgravada5'=>$sumgravada5,
                    'totalexenta'=>$sumexenta,
                    'totalcompra'=>$sumtotalitems                   
                ]);

        $updateCuentaPagar=DB::table('cuentas_a_pagar')
            ->where('idcompra', $idcompra)
            ->update(['montoapagar' => DB::raw('GREATEST(0, montoapagar - ' . (int) $sumtotalitems . ')')]);
                
         // Actualizar el estado en la tabla compra
        $udpOrdenEstado = DB::table('compra')
        ->where('idcompra', '=', $idcompra)
        ->update(['estado' => 'F']);

        return Redirect::to('compras/nota_creditoc/'.$id);

    }
}
