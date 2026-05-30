<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\VentasFormRequest;
use App\Models\Cobro;
use App\Models\CobroDetalle;
use App\Models\CuentaCobrar;
use App\Models\FormaCobroDetalle;
use App\Models\Ventas;
use App\Models\VentasDetalle;
use App\Models\Timbrado;
use App\Models\Sucursales;
use App\Models\Depositos;
use App\Services\LegalDocumentHashService;
use App\Services\MovimientoStockService;
use Illuminate\Support\Facades\Auth;


use DB;
use \DateTime;
use \DateInterval;
use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;

//include_once(app_path().'/Helpers/Funciones.php');

class VentasController extends Controller
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

        $ventas = DB::table('ventas as v')
        ->join('sucursales as s', 'v.idsucursal', '=', 's.idsucursal')
        ->join('depositos as dep', 'v.iddeposito', '=', 'dep.iddeposito')
        ->join('clientes as c', 'v.idcliente', '=', 'c.idcliente') 
        ->join('timbrado as t', 'v.idtimbrado', '=', 't.idtimbrado')   
        ->select(
            'v.idventa',
            'v.usuario',
            's.idsucursal',
            's.descripcion as sucursal',
            'dep.iddeposito',
            'dep.descripcion as deposito',
            'c.idcliente',
            'c.nombre as cliente',
            'c.num_documento',
            'v.fecha',
            'v.totaliva10',
            'v.totaliva5',
            'v.totalgravada10',
            'v.totalgravada5',
            'v.totalexenta',
            'v.totalventa',
            't.idtimbrado',
            't.nro_timbrado',
            'v.condicion',
            'v.obs',
            'v.nro_factura',
            'v.estado',
            'v.cta_cte_cliente',
            DB::raw("(SELECT COUNT(*) FROM venta_credito_aceptaciones vca WHERE vca.idventa = v.idventa AND vca.estado = 'Aceptado') as credito_aceptado"),
            DB::raw("(SELECT MAX(vca.idaceptacion_credito) FROM venta_credito_aceptaciones vca WHERE vca.idventa = v.idventa AND vca.estado = 'Aceptado') as idaceptacion_credito"),
            DB::raw("(SELECT MAX(nr.idnota_remision_venta) FROM nota_remision_venta nr WHERE nr.idventa = v.idventa AND nr.estado NOT IN ('Anulado', 'Anulada', 'A')) as idnota_remision_venta")
        )
        ->where('v.idventa', 'LIKE', '%'.$query.'%')
        ->Where('c.nombre', 'LIKE', '%'.$query2.'%')
        ->Where('v.fecha', 'LIKE', '%'.$query3.'%')
        ->Where('s.descripcion', 'LIKE', '%'.$query4.'%')
        ->Where('v.nro_factura', 'LIKE', '%'.$query5.'%')        
        ->Where('v.nro_factura', 'LIKE', '%'.$query6.'%')
        
        ->orderBy('v.idventa', 'desc')
        ->paginate(7);

           return view('ventas.venta.index',["ventas"=>$ventas,"searchText"=>$query,"searchText2"=>$query,"searchText3"=>$query,"searchText4"=>$query,"searchText5"=>$query,"searchText6"=>$query6]);
        }
    }

    public function create()
    {
        $user = Auth::user();
        $suc = $user->trabaja_sucursal;
        $timbradoId = $user->idtimbrado;
      

        $timbrados = DB::table('timbrado as t')
            ->select('t.idtimbrado', 't.nro_timbrado')
            ->where('t.idtimbrado', '=', $timbradoId)
            ->get(); 
    
        $clientes = DB::table('clientes as c')
            ->select('c.idcliente', 'c.nombre', 'c.num_documento','c.direccion')
            ->get();
    
        $sucursales = DB::table('sucursales as s')
            ->select('s.idsucursal','s.descripcion')
            ->where('s.idsucursal', '=', $suc)
            ->first();
    
        // Obtener depósitos relacionados a la sucursal seleccionada
        $sucursales = Sucursales::find($suc);
        $depositos = $sucursales->depositos()->select('iddeposito', 'descripcion')->get();
            
        // Por defecto, seleccionamos el primer depósito
        $primerDeposito = $depositos->isNotEmpty() ? $depositos->first()->iddeposito : null;

        // Obtener productos del primer depósito por defecto
        $productos = [];
        if ($primerDeposito) {
            $productos = DB::table('productos as prod')
                ->join('stock as s', 's.idproducto', '=', 'prod.idproducto')
                ->select(DB::raw('CONCAT(prod.codigo, " " ,prod.descripcion) AS producto'), 'prod.idproducto', 's.cantidad', 'prod.precio_venta')
                ->where([
                    ['prod.estado', '=', 'Activo'],
                    ['s.idsucursal', '=', $suc],
                    ['s.iddeposito', '=', $primerDeposito],
                    ['s.cantidad', '>', 0], // Agregar esta condición para el stock mayor a cero
                ])
                ->get();
        }
    
        return view("ventas.venta.create",["clientes"=>$clientes,"sucursales"=>$sucursales,"depositos"=>$depositos,"productos"=>$productos,"timbrados"=>$timbrados]);
    }
       
    private function calcularNumeroFacturaInicial($idtimbrado)
    {
        // IMPORTANTE: esto debe ejecutarse dentro de una transacción
        $timbrado = Timbrado::where('idtimbrado', $idtimbrado)
            ->lockForUpdate()
            ->firstOrFail();

        $nro_actual = $timbrado->nro_actual;
        $nro_serie  = $timbrado->nro_serie;

        if (!is_numeric($nro_actual) || $nro_actual <= 0) {
            throw new \Exception('El nro_actual del timbrado no es válido.');
        }

        $nro_factura_inicial = $nro_serie . '-' . str_pad($nro_actual, 7, '0', STR_PAD_LEFT);

        return [
            'nro_actual' => (int)$nro_actual,
            'nro_serie' => $nro_serie,
            'nro_factura_inicial' => $nro_factura_inicial,
        ];
    }

    public function store(VentasFormRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                
                $user = Auth::user();
                $idsucursal = (int) $user->trabaja_sucursal;

                $apertura = DB::table('apertura')
                    ->where('idsucursal', $idsucursal)
                    ->where('estado', 'Abierto')
                    ->orderByDesc('idapertura')
                    ->lockForUpdate()
                    ->first();

                if (!$apertura) {
                    throw new \Exception('No hay una apertura de caja abierta para registrar la venta.');
                }

                // 1) TIMBRADO: LOCK + NRO FACTURA (TU FORMATO)
                $idtimbrado = $request->get('idtimbrado');

                $fact = $this->calcularNumeroFacturaInicial($idtimbrado);
                $nro_actual = $fact['nro_actual'];
                $nro_factura_inicial = $fact['nro_factura_inicial'];

                // Reservar el siguiente nro_actual (ya está lockeado)
                $timbradoActualizado = DB::table('timbrado')
                    ->where('idtimbrado', $idtimbrado)
                    ->update(['nro_actual' => $nro_actual + 1]);

                if (!$timbradoActualizado) {
                    throw new \Exception('Error al actualizar nro_actual en la tabla timbrado.');
                }
                // 2) VALIDAR INPUT DE DETALLES
                $idproducto   = $request->input('idproducto', []);
                $cantidad     = $request->input('cantidad', []);
                $precio_venta = $request->input('precio_venta', []);

                if (empty($idproducto) || empty($cantidad) || empty($precio_venta)) {
                    throw new \Exception("Error: No se recibieron productos, cantidades o precios.");
                }

                if (count($idproducto) !== count($cantidad) || count($idproducto) !== count($precio_venta)) {
                    throw new \Exception("Error: Listas de productos/cantidades/precios con longitudes distintas.");
                }

                foreach ($cantidad as $i => $cant) {
                    if (!is_numeric($cant) || $cant <= 0) {
                        throw new \Exception("Error: La cantidad del producto con ID {$idproducto[$i]} no es válida.");
                    }
                }
                foreach ($precio_venta as $i => $pv) {
                    if (!is_numeric($pv) || $pv < 0) {
                        throw new \Exception("Error: El precio del producto con ID {$idproducto[$i]} no es válido.");
                    }
                }
                // 3) CARGAR PRODUCTOS + IMPUESTOS
                $productos = DB::table('productos as prod')
                    ->join('tipo_impuesto as ti','prod.idtipoimpuesto','=','ti.idtipoimpuesto')
                    ->select('prod.idproducto','ti.porcentaje')
                    ->whereIn('prod.idproducto', $idproducto)
                    ->get()
                    ->keyBy('idproducto');

                if ($productos->isEmpty()) {
                    throw new \Exception("Error: No se encontraron productos en la base de datos.");
                }

                foreach ($idproducto as $pid) {
                    if (!isset($productos[$pid])) {
                        throw new \Exception("Producto con ID {$pid} no encontrado.");
                    }
                }
                // CREAR CABECERA (TOTALES EN 0, SE ACTUALIZAN AL FINAL)
                $venta = new Ventas;
                $venta->idcliente     = $request->get('idcliente');
                $venta->razon_social  = $request->get('razon_social');
                $venta->num_documento = $request->get('num_documento');
                $venta->idtimbrado    = $idtimbrado;
                $venta->idsucursal    = $request->get('idsucursal');
                $venta->iddeposito    = $request->get('iddeposito');
                $venta->usuario       = $request->get('usuario');
                $venta->nro_factura   = $nro_factura_inicial;
                $venta->condicion     = $request->get('condicion');
                $venta->obs           = $request->get('obs');
                $venta->saldo_factura = 0; // se actualiza al final con el total real
                $venta->cta_cte_cliente = 'Pendiente';
                $venta->fecha         = $request->get('fecha');
                $venta->estado        = 'Pendiente'; // Pendiente hasta finalizar el cobro

                $venta->totaliva10 = 0;
                $venta->totaliva5 = 0;
                $venta->totalgravada10 = 0;
                $venta->totalgravada5 = 0;
                $venta->totalexenta = 0;
                $venta->totalventa = 0;

                $venta->save();

                // DETALLES: STOCK ATÓMICO -> GUARDAR DETALLE -> SUMAR
                $items = 1;
                $sumiva10 = 0;
                $sumiva5 = 0;
                $sumgravada10 = 0;
                $sumgravada5 = 0;
                $sumexenta = 0;
                $sumtotalitems = 0;

                for ($cont = 0; $cont < count($idproducto); $cont++) {

                    $pid  = $idproducto[$cont];
                    $cant = (float)$cantidad[$cont];
                    $pv   = (float)$precio_venta[$cont];

                    $porcentaje = (int)$productos[$pid]->porcentaje;

                    $totalitems = $cant * $pv;

                    // Cálculo impuestos
                    $iva10 = 0; $iva5 = 0;
                    $gravada10 = 0; $gravada5 = 0;
                    $exenta = 0;

                    if ($porcentaje === 10) {
                        $imp = ((100 + $porcentaje) / $porcentaje);
                        $m_gravada10 = $totalitems;

                        $iva10 = round($m_gravada10 / $imp);
                        $gravada10 = $m_gravada10 - $iva10;

                    } elseif ($porcentaje === 5) {
                        $imp = ((100 + $porcentaje) / $porcentaje);
                        $m_gravada5 = $totalitems;

                        $iva5 = round($m_gravada5 / $imp);
                        $gravada5 = $m_gravada5 - $iva5;

                        $exenta = 0;

                    } elseif ($porcentaje === 0) {
                        $exenta = $totalitems;

                    } else {
                        throw new \Exception("Tipo de impuesto no soportado ({$porcentaje}) para producto ID {$pid}.");
                    }

                    // STOCK
                    $baseStock = DB::table('stock')
                        ->where('idsucursal', $request->get('idsucursal'))
                        ->where('iddeposito', $request->get('iddeposito'))
                        ->where('idproducto', $pid);

                    $updated = (clone $baseStock)
                        ->where('cantidad', '>=', $cant)
                        ->decrement('cantidad', $cant);

                    if ($updated === 0) {
                        $exists = (clone $baseStock)->exists();
                        if (!$exists) {
                            throw new \Exception("No hay stock para el producto ID {$pid} en esa sucursal/depósito.");
                        }
                        throw new \Exception("No hay suficiente stock para el producto ID {$pid}.");
                    }

                    // Guardar detalle SOLO si stock OK
                    $detalle = new VentasDetalle();
                    $detalle->idventa      = $venta->idventa;
                    $detalle->idproducto   = $pid;
                    $detalle->cantidad     = $cant;
                    $detalle->precio_venta = $pv;
                    $detalle->items        = $items;
                    $detalle->iva10        = $iva10;
                    $detalle->iva5         = $iva5;
                    $detalle->gravada10    = $gravada10;
                    $detalle->gravada5     = $gravada5;
                    $detalle->exenta       = $exenta;
                    $detalle->totalitems   = $totalitems;
                    $detalle->save();

                    app(MovimientoStockService::class)->registrar(
                        $pid,
                        (int) $request->get('idsucursal'),
                        (int) $request->get('iddeposito'),
                        'VENTA',
                        (int) $venta->idventa,
                        'venta_detalle',
                        'SALIDA',
                        $cant,
                        null,
                        'Salida por venta',
                        $request->get('usuario')
                    );

                    // Totales reales
                    $sumiva10 += $iva10;
                    $sumiva5 += $iva5;
                    $sumgravada10 += $gravada10;
                    $sumgravada5 += $gravada5;
                    $sumexenta += $exenta;
                    $sumtotalitems += $totalitems;

                    $items++;
                }

                // ACTUALIZAR CABECERA CON TOTALES REALES
                $venta->forceFill([
                    'totaliva10'     => $sumiva10,
                    'totaliva5'      => $sumiva5,
                    'totalgravada10' => $sumgravada10,
                    'totalgravada5'  => $sumgravada5,
                    'totalexenta'    => $sumexenta,
                    'totalventa'     => $sumtotalitems,
                    'saldo_factura'  => $sumtotalitems,
                ]);
                $venta->save();

                $hashService = app(LegalDocumentHashService::class);
                $venta->forceFill([
                    'hash_documento' => $hashService->hashVenta((int) $venta->idventa),
                    'hash_version' => LegalDocumentHashService::VERSION,
                ])->save();

                // CUENTA A COBRAR
                $condicion = $request->get('condicion');
                $fechaFactura = $request->get('fecha');

                $dias = 0;
                if ($condicion !== 'Contado') {
                    preg_match_all('/\d+/', $condicion, $matches);
                    $dias = $matches ? array_sum($matches[0]) : 0;
                }

                $fechaFacturaObj = new DateTime($fechaFactura);
                $fechaVencimientoObj = (clone $fechaFacturaObj)->add(new DateInterval("P{$dias}D"));
                $fechaVencimiento = $fechaVencimientoObj->format('Y-m-d');

                DB::table('cuenta_cobrar')->insert([
                    'idventa'           => $venta->idventa,
                    'idcliente'         => $request->get('idcliente'),
                    'fecha_vencimiento' => $fechaVencimiento,
                    'fecha'             => $fechaFactura,
                    'condicion'         => $condicion,
                    'obs'               => $request->get('obs'),
                    'estado'            => 'Generado',
                    'monto_pago'        => 0,
                    'importe'           => $sumtotalitems,
                    'saldo'             => $sumtotalitems
                ]);

                $cobro = Cobro::create([
                    'idcaja'       => $apertura->idcaja,
                    'idsucursal'   => $apertura->idsucursal,
                    'idapertura'   => $apertura->idapertura,
                    'fecha_cobro'  => $fechaFactura,
                    'idcliente'    => $request->get('idcliente'),
                    'monto_cobro'  => $sumtotalitems,
                    'cobro_estado' => 'Pendiente',
                    'usuario'      => $user->name,
                ]);

                CobroDetalle::create([
                    'id_cobro'       => $cobro->id_cobro,
                    'items'          => 1,
                    'idventa'        => $venta->idventa,
                    'monto_detcobro' => $sumtotalitems,
                ]);
                return Redirect::to('ventas/venta/' . $venta->idventa);
            });

        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }  

    public function show($id)
    {
        $ventas = DB::table('ventas as v')
            ->join('sucursales as s', 'v.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'v.iddeposito', '=', 'dep.iddeposito')
            ->join('clientes as c', 'v.idcliente', '=', 'c.idcliente') 
            ->join('timbrado as t', 'v.idtimbrado', '=', 't.idtimbrado')   
            ->select(
                'v.idventa',
                'v.usuario',
                's.idsucursal',
                's.descripcion as sucursal',
                'dep.iddeposito',
                'dep.descripcion as deposito',
                'c.idcliente',
                'c.nombre as cliente',
                'c.num_documento',
                'v.fecha',
                'v.totaliva10',
                'v.totaliva5',
                'v.totalgravada10',
                'v.totalgravada5',
                'v.totalexenta',
                'v.totalventa',
                't.idtimbrado',
                't.nro_timbrado',
                'v.condicion',
                'v.obs',
                'v.nro_factura',
                'v.estado',
                'v.cta_cte_cliente',
                DB::raw("(SELECT COUNT(*) FROM venta_credito_aceptaciones vca WHERE vca.idventa = v.idventa AND vca.estado = 'Aceptado') as credito_aceptado"),
                DB::raw("(SELECT MAX(vca.idaceptacion_credito) FROM venta_credito_aceptaciones vca WHERE vca.idventa = v.idventa AND vca.estado = 'Aceptado') as idaceptacion_credito"),
                DB::raw("(SELECT MAX(nr.idnota_remision_venta) FROM nota_remision_venta nr WHERE nr.idventa = v.idventa AND nr.estado NOT IN ('Anulado', 'Anulada', 'A')) as idnota_remision_venta")
            )   
            
            ->where('v.idventa','=',$id)
            ->orderBy('v.idventa','desc')           
            ->first();

        $detalles=DB::table('venta_detalle as d')
           ->join('productos as p','d.idproducto','=','p.idproducto')
           ->select('p.descripcion as producto','d.cantidad','d.precio_venta','d.iva10','d.iva5','d.gravada10','d.gravada5','d.exenta','d.totalitems')
           ->where('d.idventa','=',$id) 
           ->get();

        $cuenta = DB::table('cuenta_cobrar')->where('idventa', $id)->first();
        $aceptacionCredito = DB::table('venta_credito_aceptaciones')
            ->where('idventa', $id)
            ->where('estado', 'Aceptado')
            ->orderByDesc('idaceptacion_credito')
            ->first();
        $notaRemision = DB::table('nota_remision_venta')
            ->where('idventa', $id)
            ->whereNotIn('estado', ['Anulado', 'Anulada', 'A'])
            ->orderByDesc('idnota_remision_venta')
            ->first();

        return view("ventas.venta.show", [
            "ventas" => $ventas,
            "detalles" => $detalles,
            "cuenta" => $cuenta,
            "aceptacionCredito" => $aceptacionCredito,
            "notaRemision" => $notaRemision,
        ]);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            // Bloquea la venta para evitar doble anulación concurrente
            $venta = Ventas::where('idventa', $id)->lockForUpdate()->firstOrFail();

            return $this->anularVentaConSincronizacion($venta, (int) $id);

        } catch (\Throwable $e) {
            DB::rollBack();
            return Redirect::to('ventas/venta')->with('error', 'Error al anular: '.$e->getMessage());
        }
    }

    private function anularVentaConSincronizacion(Ventas $venta, int $id)
    {
        if ($this->estadoEsAnulado($venta->estado)) {
            DB::rollBack();
            return Redirect::to('ventas/venta')->with('info', 'La venta ya estaba anulada.');
        }

        $pagadoNeto = (int) DB::table('det_cobro as dc')
            ->join('cobros as c', 'dc.id_cobro', '=', 'c.id_cobro')
            ->where('dc.idventa', $id)
            ->whereIn('c.cobro_estado', ['Realizado', 'R'])
            ->sum('dc.monto_detcobro');

        $pagadoNeto = max(0, $pagadoNeto);

        $detalles = DB::table('venta_detalle')
            ->where('idventa', $id)
            ->select('idproducto', 'cantidad')
            ->get();

        foreach ($detalles as $det) {
            DB::table('stock')
                ->where('idsucursal', $venta->idsucursal)
                ->where('iddeposito', $venta->iddeposito)
                ->where('idproducto', $det->idproducto)
                ->increment('cantidad', (float) $det->cantidad);

            app(MovimientoStockService::class)->registrar(
                (int) $det->idproducto,
                (int) $venta->idsucursal,
                (int) $venta->iddeposito,
                'VENTA',
                (int) $venta->idventa,
                'venta_detalle',
                'ENTRADA',
                (float) $det->cantidad,
                null,
                'Reversion por anulacion de venta',
                Auth::user()->name ?? null
            );
        }

        $cobrosPendientes = DB::table('det_cobro as dc')
            ->join('cobros as c', 'dc.id_cobro', '=', 'c.id_cobro')
            ->where('dc.idventa', $id)
            ->whereIn('c.cobro_estado', ['Pendiente', 'P'])
            ->pluck('c.id_cobro')
            ->unique()
            ->values();

        if ($cobrosPendientes->isNotEmpty()) {
            Cobro::whereIn('id_cobro', $cobrosPendientes)->get()->each(function (Cobro $cobro): void {
                $cobro->cobro_estado = 'Anulado';
                $cobro->usuario = Auth::user()->name;
                $cobro->save();
            });
        }

        if ($pagadoNeto > 0) {
            $this->registrarDevolucionCajaPorVenta($venta, $pagadoNeto);
        }

        CuentaCobrar::where('idventa', $id)
            ->lockForUpdate()
            ->get()
            ->each(function (CuentaCobrar $cuenta): void {
                $cuenta->estado = 'Anulado';
                $cuenta->importe = 0;
                $cuenta->monto_pago = 0;
                $cuenta->saldo = 0;
                $cuenta->save();
            });

        $venta->estado = 'Anulado';
        $venta->saldo_factura = 0;
        $venta->cta_cte_cliente = 'Anulado';
        $venta->hash_anulacion = app(LegalDocumentHashService::class)
            ->hashAnulacion('VENTA', (int) $venta->idventa, request('motivo_anulacion'), Auth::user()->name ?? null);
        $venta->save();

        DB::commit();

        return Redirect::to('ventas/venta')->with('success', 'Venta anulada correctamente. Se devolvio stock, se cerro la cuenta a cobrar y se ajustaron los cobros relacionados.');
    }

    private function estadoEsAnulado(?string $estado): bool
    {
        return in_array(strtoupper(trim((string) $estado)), ['A', 'ANULADO', 'ANULADA', 'CANCELADO', 'CANCELADA'], true);
    }

    private function registrarDevolucionCajaPorVenta(Ventas $venta, int $monto): void
    {
        $user = Auth::user();
        $idsucursal = (int) $user->trabaja_sucursal;

        $apertura = DB::table('apertura')
            ->where('id', $user->id)
            ->where('idsucursal', $idsucursal)
            ->where('estado', 'Abierto')
            ->orderByDesc('idapertura')
            ->lockForUpdate()
            ->first();

        if (!$apertura) {
            throw new \Exception('No hay una caja abierta para registrar la devolucion por anulacion de venta.');
        }

        $idForma = DB::table('formacobro')
            ->whereRaw("LOWER(TRIM(descripcion)) = 'efectivo'")
            ->value('id_formacobro');

        if (!$idForma) {
            $idForma = DB::table('formacobro')->min('id_formacobro');
        }

        if (!$idForma) {
            throw new \Exception('No existe ninguna forma de cobro para registrar la devolucion.');
        }

        $fecha = Carbon::now('America/Asuncion')->toDateString();
        $montoNegativo = -1 * abs($monto);

        $cobro = Cobro::create([
            'idcaja' => (int) $apertura->idcaja,
            'idsucursal' => (int) $apertura->idsucursal,
            'idapertura' => (int) $apertura->idapertura,
            'fecha_cobro' => $fecha,
            'idcliente' => (int) $venta->idcliente,
            'monto_cobro' => $montoNegativo,
            'cobro_estado' => 'Realizado',
            'usuario' => $user->name,
        ]);

        CobroDetalle::create([
            'id_cobro' => (int) $cobro->id_cobro,
            'items' => 1,
            'idventa' => (int) $venta->idventa,
            'monto_detcobro' => $montoNegativo,
        ]);

        FormaCobroDetalle::create([
            'id_cobro' => (int) $cobro->id_cobro,
            'id_formacobro' => (int) $idForma,
            'items' => 1,
            'identidademisora' => null,
            'monto_detformacobro' => $montoNegativo,
            'monto_recibido' => null,
            'vuelto' => null,
            'documento' => 'ANULACION VENTA ' . (int) $venta->idventa,
            'fecha' => $fecha,
            'fecha_vencimiento' => null,
            'idtipodocumento' => null,
        ]);
    }

    public function edit($id)
    {
        $compra=DB::table('compra as c')
            ->join('sucursal as s', 'c.idsucursal', '=', 's.idsucursal')
            ->join('deposito as dep', 'c.iddeposito', '=', 'dep.iddeposito')
            ->join('proveedor as p', 'c.idproveedor', '=', 'p.idproveedor')        
            ->select('c.idcompra', 'c.usu_inser', 's.idsucursal', 's.descripcion as sucursal', 'dep.iddeposito', 'dep.descripcion as deposito', 'p.idproveedor', 'p.nombre as proveedor', 'p.num_documento','c.idordencompra', 'c.fecha_registro', 'c.totaliva10', 'c.totaliva5', 'c.totalgravada10', 'c.totalgravada5', 'c.totalexenta', 'c.totalcompra', 'c.timbrado', 'c.condicion', 'c.concepto' , 'c.nro_factura','c.estado','c.fecha_factura','c.fecha_vencimiento')
           ->where('c.idcompra','=',$id)
           ->orderBy('c.idcompra','desc')           
           ->first();

        $detalles=DB::table('compra_detalle as d')
           ->join('articulo as a','d.idarticulo','=','a.idarticulo')
           ->select('idcompra_detalle','a.idarticulo','a.nombre as articulo','d.cantidad','d.precio_compra','d.iva10','d.iva5','d.gravada10','d.gravada5','d.exenta','d.totalitems')
           ->where('d.idcompra','=',$id) 
           ->get();
           return view("compras.compra.edit",["compra"=>$compra,"detalles"=>$detalles]);
    }

    

    public function imprimirfactura(Request $request, $idventa)
    {     
        $venta = DB::table('ventas as v')
            ->join('sucursales as s', 'v.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'v.iddeposito', '=', 'dep.iddeposito')
            ->join('clientes as c', 'v.idcliente', '=', 'c.idcliente') 
            ->join('ciudades as ciu', 'c.idciudad', '=', 'ciu.idciudad') // Join con la tabla ciudad
            ->join('timbrado as t', 'v.idtimbrado', '=', 't.idtimbrado')   
            ->select('v.idventa', 'v.usuario', 's.idsucursal', 's.descripcion as sucursal', 'dep.iddeposito', 'dep.descripcion as deposito', 'c.idcliente', 'c.nombre as cliente', 'c.num_documento', 'c.direccion', 'c.idciudad', 'ciu.descripcion as ciudad', 'c.telefono' , 'v.fecha', 'v.totaliva10', 'v.totaliva5', 'v.totalgravada10', 'v.totalgravada5', 'v.totalexenta', 'v.totalventa', 't.idtimbrado', 't.nro_timbrado', 't.fecha_inicial', 't.fecha_vencimiento' ,'v.condicion', 'v.obs' , 'v.nro_factura','v.estado','v.cta_cte_cliente', 'v.hash_documento', 'v.hash_anulacion', 'v.hash_version')
            ->where('v.idventa', $idventa)
            ->orderBy('v.idventa','asc')
            ->first();

        $venta_detalle = DB::table('venta_detalle as d')
            ->join('productos as p', 'd.idproducto', '=', 'p.idproducto')
            ->join('ventas as v', 'd.idventa', '=', 'v.idventa')
            ->select('d.idventa', 'd.items' ,'p.descripcion as producto', 'd.cantidad', 'd.precio_venta', 'd.iva10', 'd.iva5', 'd.gravada10', 'd.gravada5', 'd.exenta', 'd.totalitems')
            ->where('d.idventa', $idventa)
            ->orderBy('d.idventa', 'asc')
            ->get();
        
            

        $hashService = app(LegalDocumentHashService::class);
        $hashValido = $venta
            ? $hashService->verificar($venta->hash_documento ?? null, $hashService->hashVenta((int) $venta->idventa))
            : false;

        return view('ventas.venta.generado',["venta"=>$venta,"venta_detalle"=>$venta_detalle,"hashValido"=>$hashValido]);
    }
}
