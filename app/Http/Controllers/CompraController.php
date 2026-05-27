<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ComprasFormRequest;
use App\Models\Compras;
use App\Models\ComprasDetalle;
use App\Models\OrdenCompras;
use App\Models\OrdenComprasDetalle;
use App\Models\Productos;
use App\Models\Proveedores;
use App\Models\Sucursales;
use App\Models\Depositos;
use DB;
use App\Models\Stock;
use Log;
use Exception;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;

class CompraController extends Controller
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

        $queryBuilder = DB::table('compras as c')
                            ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
                            ->join('depositos as dep', 'c.iddeposito', '=', 'dep.iddeposito')
                            ->join('proveedores as p', 'c.idproveedor', '=', 'p.idproveedor')
                            ->leftJoin('orden_compras as oc', 'c.idordencompra', '=', 'oc.idordencompra')
                            ->select(
                                'c.idcompra', 
                                's.idsucursal',
                                's.descripcion as sucursal',
                                'dep.iddeposito',
                                'dep.descripcion as deposito',  
                                'p.idproveedor',
                                'p.razonsocial as proveedor', 
                                'p.ruc', 
                                'oc.idordencompra',
                                'c.fecha',
                                'c.totaliva10', 
                                'c.totaliva5', 
                                'c.totalgravada10', 
                                'c.totalgravada5', 
                                'c.totalexenta',
                                'c.totalcompra',
                                'c.timbrado',
                                'c.condicion',
                                'c.concepto',
                                'c.nro_factura',
                                'c.fecha_factura',
                                'c.fecha_vencimiento',
                                'c.usuario',
                                'c.estado'
                            );

        // Aplicar condiciones WHERE según los términos de búsqueda
        if ($query) {
            $queryBuilder->where('c.idcompra', 'LIKE', '%' . $query . '%');
        }
    
        if ($query2) {
            $queryBuilder->where('c.fecha', 'LIKE', '%' . $query2 . '%');
        }

        if ($query3) {
            $queryBuilder->where('s.descripcion', 'LIKE', '%' . $query3 . '%');
        }

        if ($query4) {
            $queryBuilder->where('p.razonsocial', 'LIKE', '%' . $query4 . '%');
        }
    
        if ($query5) {
            $queryBuilder->where('oc.idordencompra', 'LIKE', '%' . $query5 . '%');
        }

        if ($query6) {
            $queryBuilder->where('p.ruc', 'LIKE', '%' . $query6 . '%');
        }

        if ($query7) {
            $queryBuilder->where('c.estado', 'LIKE', '%' . $query7 . '%');
        }                    

        $compras = $queryBuilder->orderBy('c.idcompra', 'desc')
        ->groupBy(
            'c.idcompra', 
            's.idsucursal',
            's.descripcion',
            'dep.iddeposito',
            'dep.descripcion',  
            'p.idproveedor', 
            'p.razonsocial', 
            'p.ruc',
            'p.direccion',
            'oc.idordencompra',
            'c.fecha', 
            'c.totaliva10', 
            'c.totaliva5', 
            'c.totalgravada10', 
            'c.totalgravada5', 
            'c.totalexenta',
            'c.totalcompra',
            'c.timbrado',
            'c.condicion',
            'c.concepto',
            'c.nro_factura',
            'c.fecha_factura',
            'c.fecha_vencimiento',
            'c.usuario',
            'c.estado'
        )
        ->paginate(7); // Paginar directamente

         // Obtener total de registros (con los filtros aplicados)
         $total = $compras->total();

         return view('compras.compra.index', [
             "compras" => $compras,
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

        $ordenes=DB::table('orden_compras as oc') 
        ->join('sucursales as s', 'oc.idsucursal', '=', 's.idsucursal') 
        ->join('proveedores as p', 'oc.idproveedor', '=', 'p.idproveedor')  
        ->select('oc.idordencompra','s.idsucursal','s.descripcion','p.idproveedor','p.razonsocial','p.ruc','p.direccion','oc.fecha','oc.estado') 
        ->where('oc.idsucursal', '=', $suc)
        ->where('oc.estado', '=', 'Pendiente')
        ->get();    

        $proveedores=DB::table('proveedores as p')        
        ->select('p.idproveedor', 'p.razonsocial', 'p.ruc','p.direccion')        
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
        ->select(DB::raw('CONCAT(prod.codigo, " " ,prod.descripcion) AS producto'),'prod.idproducto')
        ->where('prod.estado','=','Activo')
        ->get();
        return view("compras.compra.create",["proveedores"=>$proveedores,"sucursales"=>$sucursales,"depositos"=>$depositos,"productos"=>$productos,"ordenes"=>$ordenes]);
    }

    public function store (ComprasFormRequest $request)
    {        
        if (!$request->input('ruc')) {
            return redirect()->back()->withErrors(['ruc' => 'Debe seleccionar un proveedor con un RUC válido.']);
        }

        try {
            DB::beginTransaction();
                $compra=new Compras;
                $compra->idproveedor=$request->get('idproveedor');                
                $compra->idordencompra=$request->get('idordencompra');
                $compra->idsucursal=$request->get('idsucursal'); 
                $compra->iddeposito=$request->get('iddeposito');         
                $compra->usuario=$request->get('usuario');
                $compra->nro_factura=$request->get('nro_factura');
                $compra->condicion=$request->get('condicion');                
                $compra->concepto=$request->get('concepto');
                $compra->timbrado=$request->get('timbrado');
                $compra->ruc=$request->get('ruc');
                $compra->totaliva10=$request->get('totaliva10');
                $compra->totaliva5=$request->get('totaliva5');
                $compra->totalgravada10=$request->get('totalgravada10');
                $compra->totalgravada5=$request->get('totalgravada5');
                $compra->totalexenta=$request->get('totalexenta');
                $compra->totalcompra=$request->get('totalcompra');
                                   
                $mytime = Carbon::now('America/Asuncion');                 
                $compra->fecha=$request->get('fecha');
                $compra->fecha_factura = $request->get('fecha_factura');
                $compra->fecha_vencimiento = $request->get('fecha_vencimiento');

                $compra->estado='Realizado';
               
                $compra->save();

                // Verifica si el ID de la compra fue generado correctamente
                if (!$compra->idcompra) {
                    throw new Exception('No se generó el ID de la compra.');
                }

                $idproducto = $request->get('idproducto');
                $cantidad = $request->get('cantidad');
                $precio_compra = $request->get('precio_compra');
                

                //  select  al producto con inner join de tipo impuesto de lo que te trae tu $idproducto[$cont]
                $productos = DB::table('productos as prod')
                ->join('tipo_impuesto as ti','prod.idtipoimpuesto','=','ti.idtipoimpuesto')
                ->select('prod.idproducto','ti.porcentaje')
                ->whereIn('prod.idproducto',$idproducto)
                ->get();

                
                $cont = 0;
                $items = 1;
                $sumiva10=0;
                $sumiva5=0;
                $sumgravada10=0;
                $sumgravada5=0;
                $sumexenta=0;
                $sumtotalitems=0;

                while ($cont < count($idproducto)) {
                    if (!isset($idproducto[$cont]) || !isset($cantidad[$cont]) || !isset($precio_compra[$cont]) || !is_numeric($cantidad[$cont]) || !is_numeric($precio_compra[$cont])) {
                        throw new Exception("Error en los datos del producto en la posición {$cont}");
                    }
                    
                    $prod= $productos->where('idproducto','=',$idproducto[$cont])->first();
                   
                    if (!$prod) {
                        throw new Exception("Producto con ID {$idproducto[$cont]} no encontrado.");
                    }
    
                    if (!isset($precio_compra[$cont])) {
                        throw new Exception("Error: El precio de compra no está definido en el índice $cont.");
                    }
    
                    if (!isset($cantidad[$cont])) {
                        throw new Exception("Error: La cantidad no está definida en el índice $cont.");
                    }
                                        
                    $porcentaje = $prod->porcentaje;
                    $totalitems=0;
                    $totalitems= $cantidad[$cont]*$precio_compra[$cont];
                    
                   
                    
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

                        $detalle = new ComprasDetalle();
                        $detalle->idcompra= $compra->idcompra;
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

                        $stock = DB::table('stock')
                            ->where('idsucursal', $request->get('idsucursal'))
                            ->where('iddeposito', $request->get('iddeposito'))
                            ->where('idproducto', $idproducto[$cont])
                            ->first();

                        if ($stock) {
                            // El stock existe, entonces actualizamos la cantidad
                            DB::table('stock')
                                ->where('idsucursal', $request->get('idsucursal'))
                                ->where('iddeposito', $request->get('iddeposito'))
                                ->where('idproducto', $idproducto[$cont])
                                ->increment('cantidad', $cantidad[$cont]);
                        } else {
                            // El stock no existe, entonces insertamos un nuevo registro
                            DB::table('stock')->insert([
                                'idsucursal' => $request->get('idsucursal'),
                                'iddeposito' => $request->get('iddeposito'),
                                'idproducto' => $idproducto[$cont],
                                'cantidad' => $cantidad[$cont]
                            ]);
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

                $compra->forceFill([
                    'totaliva10' => $sumiva10,
                    'totaliva5' => $sumiva5,
                    'totalgravada10' => $sumgravada10,
                    'totalgravada5' => $sumgravada5,
                    'totalexenta' => $sumexenta,
                    'totalcompra' => $sumtotalitems,
                ]);
                $compra->save();


                $insertarctapagar = DB::table('cuentas_a_pagar')->insertGetId([
                    'idcompra' => $compra->idcompra,
                    'idproveedor' => $request->get('idproveedor'),
                    'idsucursal' => $request->get('idsucursal'),                    
                    'fecha_vencimiento' => $request->get('fecha_vencimiento'), 
                    'fecha_factura' => $request->get('fecha_factura'), 
                    'montopagado' => $sumtotalitems,
                    'montoapagar' => $sumtotalitems,
                    'estado' => 'Pendiente'
                    
                ]);
                
            DB::commit();
            
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        return Redirect::to('compras/compra');

    }
   

    public function show($id)
    {
        $compra=DB::table('compras as c')
            ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'c.iddeposito', '=', 'dep.iddeposito')
            ->join('proveedores as p', 'c.idproveedor', '=', 'p.idproveedor')
            ->leftJoin('orden_compras as oc', 'c.idordencompra', '=', 'oc.idordencompra')    
            ->select('c.idcompra', 'c.usuario', 's.idsucursal', 's.descripcion as sucursal', 'dep.iddeposito', 'dep.descripcion as deposito', 'p.idproveedor', 'p.razonsocial as proveedor', 'p.ruc','c.idordencompra', 'c.fecha', 'c.totaliva10', 'c.totaliva5', 'c.totalgravada10', 'c.totalgravada5', 'c.totalexenta', 'c.totalcompra', 'c.timbrado', 'c.condicion', 'c.concepto' , 'c.nro_factura','c.estado','c.fecha_factura','c.fecha_vencimiento')
           ->where('c.idcompra','=',$id)
           ->orderBy('c.idcompra','desc')
           ->groupBy('c.idcompra', 'c.usuario', 's.idsucursal', 's.descripcion', 'dep.iddeposito', 'dep.descripcion', 'p.idproveedor', 'p.razonsocial', 'p.ruc','c.idordencompra', 'c.fecha', 'c.totaliva10', 'c.totaliva5', 'c.totalgravada10', 'c.totalgravada5', 'c.totalexenta', 'c.totalcompra', 'c.timbrado', 'c.condicion', 'c.concepto' , 'c.nro_factura','c.estado','c.fecha_factura','c.fecha_vencimiento')
           ->first();

        $detalles=DB::table('compra_detalle as d')
           ->join('productos as prod','d.idproducto','=','prod.idproducto')
           ->select('prod.descripcion as producto','d.cantidad','d.precio_compra','d.iva10','d.iva5','d.gravada10','d.gravada5','d.exenta','d.totalitems')
           ->where('d.idcompra','=',$id) 
           ->get();

        return view("compras.compra.show",["compra"=>$compra,"detalles"=>$detalles]);
    }

    public function destroy($id)
    {
        //ComprasDetalle::where('idcompra', $id)->delete();
        try {
            $compra = Compras::findOrFail($id);
            $compra->estado = 'Cancelado';
            $compra->save();

            return Redirect::to('compras/compra')->with('success', 'Compra cancelada correctamente.');
        } catch (Exception $e) {
            return Redirect::to('compras/compra')->with('error', 'Error al cancelar la compra.');
        }
    }

    public function insertar_ordenes (Request $request)
    {
        // Validación de campos
        $validator = Validator::make($request->all(), [
            'numero_orden' => 'required',
            'nro_factura'=>'required',
            'timbrado'=>'required',         
            'condicion' => 'required'            
        ]);

        // Manejo de errores de validación
        if ($validator->fails()) {
            $errors = $validator->errors();
            $error_message = implode('<br>', $errors->all());
        
            return response()->json(['error' => $error_message], 400);
        }
        $idordencompra = $request->get('numero_orden');   
        $nro_factura = $request->get('nro_factura');        
        $condicion = $request->get('condicion');
        $concepto = $request->get('concepto');
        $timbrado = $request->get('timbrado');
        $fecha_factura = $request->get('fecha_factura');
        $fecha_vencimiento = $request->get('fecha_vencimiento');
        //return dd( $iddeposito);
        // Asigna el resultado de la consulta a la variable $orden
        $ordenes = DB::table('orden_compras as oc')        
            ->select('oc.idordencompra','oc.idsucursal','oc.idproveedor','oc.iddeposito')
            ->where('oc.idordencompra','=',$idordencompra)
            ->first();
        
        if ($ordenes) {
            // Traer proveedor por first() para poder capturar su ruc
            $proveedores = DB::table('proveedores as p')
                ->select('p.idproveedor', 'p.razonsocial', 'p.ruc', 'p.direccion')
                ->where('p.idproveedor', '=', $ordenes->idproveedor)
                ->first();
            //return dd($ordenes);
        } else {
            // Manejar el caso en el que $ordenes es nulo
            $error_message = "No se encontraron datos para el número de orden proporcionado.";
            // Puedes agregar más información al mensaje de error según tus necesidades.
            return response()->json(['error' => $error_message], 404);
            // Puedes cambiar el código de respuesta y el formato del mensaje según tus requisitos.
                }
            //return dd($ordenes);
        // Insertar en la tabla compra
        $user = Auth::user()->name;
        $suc = Auth::user()->trabaja_sucursal;
        $compra = Compras::create([
            'idordencompra' => $ordenes->idordencompra,
            'idproveedor' => $proveedores->idproveedor,
            'ruc' => $proveedores->ruc,            
            'idsucursal' => $suc,
            'usuario' => $user,            
            'iddeposito' => $ordenes->iddeposito, //Así estiro mi deposito de mi orden...
            'fecha' => now(),
            'fecha_factura' => $fecha_factura,
            'fecha_vencimiento' => $fecha_vencimiento,
            'estado' => 'Realizado',  // Establece un valor por defecto
            'nro_factura' => $nro_factura,
            'timbrado' => $timbrado,
            'condicion' => $condicion,
            'concepto' => $concepto
        ]);
        //return dd( $inscab);
        $idcaborden = $compra->idcompra;
        //dd($idcaborden); 
        // vas a traer mediante un get() el detalle de los presupuestos 
        $orden_detalle=DB::table('orden_detalle as od')        
        ->select('od.idorden_detalle', 'od.idordencompra', 'od.items','od.idproducto','od.cantidad','od.precio_compra')
        ->where('od.idordencompra', '=', $idordencompra)
        ->get();
        //dd($orden_detalle);
         
        // Antes de la inserción, verifica que $idcaborden existe en la tabla presupuestocompra
        
        // Insertar detalles en la tabla compra_detalle
        foreach ($orden_detalle as $detalle) {
            ComprasDetalle::create([
                'idcompra' => $idcaborden,
                'items' => $detalle->items,
                'idproducto' => $detalle->idproducto,
                'cantidad' => $detalle->cantidad,
                'precio_compra' => $detalle->precio_compra
            ]);
        }
       
        return Redirect::to('compras/compra/'.$idcaborden.'/edit');

    }

    public function edit($id)
    {
        $compra=DB::table('compras as c')
            ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'c.iddeposito', '=', 'dep.iddeposito')
            ->join('proveedores as p', 'c.idproveedor', '=', 'p.idproveedor')        
            ->select('c.idcompra', 'c.usuario', 's.idsucursal', 's.descripcion as sucursal', 'dep.iddeposito', 'dep.descripcion as deposito', 'p.idproveedor', 'p.razonsocial as proveedor', 'p.ruc','c.idordencompra', 'c.fecha', 'c.totaliva10', 'c.totaliva5', 'c.totalgravada10', 'c.totalgravada5', 'c.totalexenta', 'c.totalcompra', 'c.timbrado', 'c.condicion', 'c.concepto' , 'c.nro_factura','c.estado','c.fecha_factura','c.fecha_vencimiento')
           ->where('c.idcompra','=',$id)
           ->orderBy('c.idcompra','desc')           
           ->first();

        $detalles=DB::table('compra_detalle as d')
           ->join('productos as prod','d.idproducto','=','prod.idproducto')
           ->select('d.idcompra_detalle','prod.idproducto','prod.descripcion as producto','d.cantidad','d.precio_compra','d.iva10','d.iva5','d.gravada10','d.gravada5','d.exenta','d.totalitems')
           ->where('d.idcompra','=',$id) 
           ->get();
           return view("compras.compra.edit",["compra"=>$compra,"detalles"=>$detalles]);
           
    }

    public function update(Request $request, $id)
    {
        // Validación adicional en el lado del servidor
        $request->validate([
            'precio_compra.*' => 'required|numeric|min:1', // Asegura que todos los precios_compra sean numéricos y mayores a 0
        ]);

        $idcompra_detalle = $request->get('idcompra_detalle');
        $cantidad = $request->get('cantidad');
        $precio_compra = $request->get('precio_compra');
        $idproducto = $request->get('idproducto');

        // return dd(['a'=>$cantidad,'b'=>$precio_compra,'c'=>$idproducto, 'd'=>$idcompra_detalle]);
        
        $productos = DB::table('productos as prod')
        ->join('tipo_impuesto as ti','prod.idtipoimpuesto','=','ti.idtipoimpuesto')
        ->select('prod.idproducto','ti.porcentaje')
        ->whereIn('prod.idproducto',$idproducto)
        ->get();
        
        // return dd(['a'=>$productos,'b'=>$precio,'c'=>$idproducto, 'd'=>$idpresupuestoc_detalle]);
           
         // Obtén los valores necesarios de la cabecera de la compra
         $compraCabecera = DB::table('compras')
         ->where('idcompra', '=', $id)
         ->select('idproveedor', 'idsucursal', 'fecha_vencimiento', 'fecha_factura', 'fecha', 'idordencompra', 'iddeposito')
         ->first();
 
         // Verifica si la compra existe antes de continuar
        $idproveedor = $compraCabecera->idproveedor;
        $idsucursal = $compraCabecera->idsucursal;
        $iddeposito = $compraCabecera->iddeposito;
        $fecha_factura = $compraCabecera->fecha_factura;
        $fecha_vencimiento = $compraCabecera->fecha_vencimiento;
        $fecha = $compraCabecera->fecha;
        $idordencompra = $compraCabecera->idordencompra;
          
          

        $prod=0;
        $cont = 0;
        $items = 1;
        $sumiva10=0;
        $sumiva5=0;
        $sumgravada10=0;
        $sumgravada5=0;
        $sumexenta=0;
        $sumtotalitems=0;

        foreach ($idcompra_detalle as $key => $value) {
            
            $prod= $productos->where('idproducto', '=' ,$idproducto[$value])->first();
                        
            $porcentaje = $prod->porcentaje;
            $totalitems=0;
            $totalitems= $cantidad[$value]*$precio_compra[$value];
                                    
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
            $detalleCompra = ComprasDetalle::findOrFail($value);
            $detalleCompra->forceFill([
                'cantidad' => $cantidad[$value],
                'precio_compra' => $precio_compra[$value],
                'iva10' => $iva10,
                'iva5' => $iva5,
                'gravada10' => $gravada10,
                'gravada5' => $gravada5,
                'exenta' => $exenta,
                'totalitems' => $totalitems,
            ]);
            $detalleCompra->save();


                $con_stk = DB::select("SELECT cantidad FROM stock WHERE idsucursal = ? AND iddeposito = ? AND idproducto = ?", [$idsucursal, $iddeposito, $idproducto[$value]]);

                $existe = 0;
                $existencia = 0;
                
                foreach ($con_stk as $cst) {
                    $existe = 1;
                    $existencia = $cst->cantidad; // Cambiamos a 'cantidad' porque 'existencia' tampoco existe en tu tabla.
                }
                
                if ($existe == 1) {
                    // Actualizar el stock existente
                    DB::update("UPDATE stock SET cantidad = cantidad + ? WHERE idsucursal = ? AND iddeposito = ? AND idproducto = ?", [$cantidad[$value], $idsucursal, $iddeposito, $idproducto[$value]]);
                } else {
                    // Insertar un nuevo registro en stock
                    DB::insert("INSERT INTO stock (idsucursal, iddeposito, idproducto, cantidad) VALUES (?, ?, ?, ?)", [$idsucursal, $iddeposito, $idproducto[$value], $cantidad[$value]]);
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

        // Validación adicional en el lado del servidor
        $request->validate([
            'precio_compra.*' => 'required|numeric|min:1', // Asegura que todos los precios_compra sean numéricos y mayores a 0
        ]);
                
        $compra = Compras::findOrFail($id);
        $compra->forceFill([
            'totaliva10' => $sumiva10,
            'totaliva5' => $sumiva5,
            'totalgravada10' => $sumgravada10,
            'totalgravada5' => $sumgravada5,
            'totalexenta' => $sumexenta,
            'totalcompra' => $sumtotalitems,
            'concepto' => $request->get('concepto'),
            'estado' => 'Pendiente',
        ]);
        $compra->save();


        // Inserta en la tabla cuenta_a_pagar
        $insertarctapagar = DB::table('cuentas_a_pagar')->insertGetId([
            'idcompra' => $id,
            'idproveedor' => $idproveedor,
            'idsucursal' => $idsucursal,
            'fecha_vencimiento' => $fecha_vencimiento,
            'fecha_factura' => $fecha_factura,
            'montopagado' => $sumtotalitems,
            'montoapagar' => $sumtotalitems,
            'estado' => 'Pendiente'
        ]);

        // Actualizar el estado en la tabla ordencompra
        $udpOrdenEstado = DB::table('orden_compras')
        ->where('idordencompra', '=', $idordencompra)
        ->update(['estado' => 'Realizado']);
              
        return Redirect::to('compras/compra/'.$id);

    }
}
