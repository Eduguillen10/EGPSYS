<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\NotaCreditoVRequest;
use App\Models\Cobro;
use App\Models\CobroDetalle;
use App\Models\FormaCobroDetalle;
use App\Models\NotaCreditoV;
use App\Models\NotaCreditoVDetalle;
use App\Models\Ventas;
use DB;
use App\Models\Stock;
use App\Models\Sucursales;
use App\Services\LegalDocumentHashService;
use App\Services\MovimientoStockService;
use Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;

class NotaCreditoVController extends Controller
{
    private const ESTADOS_ANULADOS = ['A', 'Anulado', 'Anulada', 'Cancelado', 'Cancelada'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request) {
            $query = $request->get('searchText');
            $query2 = $request->get('searchText2');
            $query3 = $request->get('searchText3');
            $query4 = $request->get('searchText4');
            $query5 = $request->get('searchText5');
            $query6 = $request->get('searchText6');

            $nota_creditov = DB::table('nota_credito_venta as c')
                ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
                ->join('depositos as dep', 'c.iddeposito', '=', 'dep.iddeposito')
                ->join('clientes as cli', 'c.idcliente', '=', 'cli.idcliente')
                ->join('users as u', 'c.idusuario', '=', 'u.id')
                ->select('c.idnota_creditov', 'c.nro_nota_credito', 'c.idventa', 'u.name as usuario', 's.idsucursal', 's.descripcion as sucursal', 'dep.iddeposito', 'dep.descripcion as deposito', 'cli.idcliente', 'cli.nombre as cliente', 'cli.num_documento', 'c.fecha_registro', 'c.totaliva10', 'c.totaliva5', 'c.totalgravada10', 'c.totalgravada5', 'c.totalexenta', 'c.totalventa', 'c.timbrado', 'c.condicion', 'c.concepto', 'c.nro_factura', 'c.estado', 'c.fecha_factura', 'c.fecha_vencimiento')
                ->where('c.idnota_creditov', 'LIKE', '%' . $query . '%')
                ->Where('cli.nombre', 'LIKE', '%' . $query2 . '%')
                ->Where('c.fecha_registro', 'LIKE', '%' . $query3 . '%')
                ->Where('s.descripcion', 'LIKE', '%' . $query4 . '%')
                ->where(function ($q) use ($query5) {
                    $q->where('c.nro_nota_credito', 'LIKE', '%' . $query5 . '%')
                        ->orWhere('c.nro_factura', 'LIKE', '%' . $query5 . '%');
                })
                ->Where('cli.num_documento', 'LIKE', '%' . $query6 . '%')

                ->orderBy('c.idnota_creditov', 'desc')
                ->paginate(7);

            return view('ventas.nota_creditov.index', ["nota_creditov" => $nota_creditov, "searchText" => $query, "searchText2" => $query2, "searchText3" => $query3, "searchText4" => $query4, "searchText5" => $query5, "searchText6" => $query6]);
        }
    }

    public function create(Request $request)
    {
        $suc = Auth::user()->trabaja_sucursal;

        $venta = DB::table('ventas as v')
            ->join('sucursales as s', 'v.idsucursal', '=', 's.idsucursal')
            ->join('clientes as cli', 'v.idcliente', '=', 'cli.idcliente')
            ->join('depositos as dep', 'v.iddeposito', '=', 'dep.iddeposito')
            ->join('timbrado as t', 'v.idtimbrado', '=', 't.idtimbrado')
            ->join('users as u', 'v.idusuario', '=', 'u.id')
            ->select('v.idventa', 'u.name as usuario', 's.idsucursal', 's.descripcion as sucursal', 'dep.iddeposito', 'dep.descripcion as deposito', 'cli.idcliente', 'cli.nombre as cliente', 'cli.num_documento', 'v.fecha', 'v.totaliva10', 'v.totaliva5', 'v.totalgravada10', 'v.totalgravada5', 'v.totalexenta', 'v.totalventa', 't.idtimbrado', 't.nro_timbrado', 'v.condicion', 'v.obs', 'v.nro_factura', 'v.estado')
            ->where('v.idsucursal', '=', $suc)
            ->whereIn('v.estado', ['Realizado', 'R'])
            ->get();

        $clientes = DB::table('clientes as cli')
            ->select('cli.idcliente', 'cli.nombre', 'cli.num_documento', 'cli.direccion')
            ->get();
        $sucursales = DB::table('sucursales as s')
            ->select('s.idsucursal', 's.descripcion')
            ->where('s.idsucursal', '=', $suc)
            ->first();
        // Obtener depÃ³sitos relacionados a la sucursal seleccionada
        $sucursales = Sucursales::find($suc);
        $depositos = $sucursales->depositos()->select('iddeposito', 'descripcion')->get();

        // Por defecto, seleccionamos el primer depÃ³sito
        $primerDeposito = $depositos->isNotEmpty() ? $depositos->first()->iddeposito : null;

        $productos = DB::table('productos as prod')
            ->select(DB::raw('CONCAT(prod.codigo, " " ,prod.descripcion) AS producto'), 'prod.idproducto')
            ->where('prod.estado', '=', 'Activo')
            ->get();

        // ObtÃ©ner las facturas ya almacenadas
        $facturas_ya_almacenadas = NotaCreditoV::whereNotIn('estado', ['A', 'Anulado', 'Anulada', 'Cancelado', 'Cancelada'])
            ->pluck('idventa')
            ->toArray();

        $facturaSeleccionada = null;
        $detallesSeleccionados = collect();

        if ($request->filled('idventa')) {
            $facturaSeleccionada = DB::table('ventas as v')
                ->join('clientes as cli', 'v.idcliente', '=', 'cli.idcliente')
                ->join('timbrado as t', 'v.idtimbrado', '=', 't.idtimbrado')
                ->select('v.idventa', 'v.idcliente', 'v.iddeposito', 'v.num_documento', 'v.nro_factura', 'v.condicion', 't.nro_timbrado')
                ->where('v.idventa', (int) $request->get('idventa'))
                ->first();

            $detallesSeleccionados = DB::table('venta_detalle as vd')
                ->join('productos as prod', 'vd.idproducto', '=', 'prod.idproducto')
                ->select('vd.idproducto', DB::raw('CONCAT(prod.codigo, " " ,prod.descripcion) AS producto'), 'vd.cantidad', 'vd.precio_venta')
                ->where('vd.idventa', (int) $request->get('idventa'))
                ->orderBy('vd.items')
                ->get();
        }

        $timbradoActivo = $this->timbradoActivo((int) $suc);
        $previewTimbrado = $timbradoActivo->nro_timbrado ?? '';
        $previewNroNota = $timbradoActivo
            ? $this->generarNumeroNota($this->proximoIdTabla('nota_credito_venta', 'idnota_creditov'), $timbradoActivo->nro_serie)
            : 'Sin timbrado activo';

        return view("ventas.nota_creditov.create", [
            "clientes" => $clientes,
            "sucursales" => $sucursales,
            "depositos" => $depositos,
            "productos" => $productos,
            "venta" => $venta,
            'facturas_ya_almacenadas' => $facturas_ya_almacenadas,
            'facturaSeleccionada' => $facturaSeleccionada,
            'detallesSeleccionados' => $detallesSeleccionados,
            'fechaFacturaSeleccionada' => $request->get('fecha'),
            'conceptoSeleccionado' => $request->get('concepto'),
            'previewNroNota' => $previewNroNota,
            'previewTimbrado' => $previewTimbrado,
        ]);
    }

    public function store(NotaCreditoVRequest $request)
    {
        try {
            DB::beginTransaction();
            $timbradoActivo = $this->timbradoActivo((int) $request->get('idsucursal'));

            if (! $timbradoActivo) {
                throw new \RuntimeException('No existe timbrado activo para la sucursal seleccionada.');
            }

            $nota_creditov = new NotaCreditoV;
            $nota_creditov->idcliente = $request->get('idcliente');
            $nota_creditov->num_documento = $request->get('num_documento');
            $nota_creditov->idventa = $request->get('idventa');
            $nota_creditov->idsucursal = $request->get('idsucursal');
            $nota_creditov->iddeposito = $request->get('iddeposito');
            $nota_creditov->idusuario = Auth::id();
            $nota_creditov->nro_factura = $request->get('nro_factura');
            //$nota_creditov->condicion=$request->get('condicion');   *****************************************  ver esto             
            $nota_creditov->concepto = $request->get('concepto');
            $nota_creditov->timbrado = $timbradoActivo->nro_timbrado;
            $nota_creditov->totaliva10 = $request->get('totaliva10');
            $nota_creditov->totaliva5 = $request->get('totaliva5');
            $nota_creditov->totalgravada10 = $request->get('totalgravada10');
            $nota_creditov->totalgravada5 = $request->get('totalgravada5');
            $nota_creditov->totalexenta = $request->get('totalexenta');
            $nota_creditov->totalventa = $request->get('totalventa');

            $mytime = Carbon::now('America/Asuncion');
            $nota_creditov->fecha_factura = $request->get('fecha_factura');
            $nota_creditov->fecha_registro = now();

            $nota_creditov->estado = 'Realizado';

            //return dd($nota_creditov);

            $nota_creditov->save();
            $nota_creditov->nro_nota_credito = $this->generarNumeroNota($nota_creditov->idnota_creditov, $timbradoActivo->nro_serie);
            $nota_creditov->save();

            $idproducto = $request->get('idproducto');
            $cantidad = $request->get('cantidad');
            $precio_venta = $request->get('precio_venta');


            //  select  al producto con inner join de tipo impuesto de lo que te trae tu $idproducto[$cont]
            $productos = DB::table('productos as prod')
                ->join('tipo_impuesto as ti', 'prod.idtipoimpuesto', '=', 'ti.idtipoimpuesto')
                ->select('prod.idproducto', 'ti.porcentaje')
                ->whereIn('prod.idproducto', $idproducto)
                ->get();


            $prod = 0;
            $cont = 0;
            $items = 1;
            $sumiva10 = 0;
            $sumiva5 = 0;
            $sumgravada10 = 0;
            $sumgravada5 = 0;
            $sumexenta = 0;
            $sumtotalitems = 0;

            while ($cont < count($idproducto)) {
                $prod = $productos->where('idproducto', '=', $idproducto[$cont])->first();
                //Return dd($prod);

                //  calculo de los demas campos que estÃ¡ en el notepad
                $porcentaje = $prod->porcentaje;
                $totalitems = 0;
                $totalitems = $cantidad[$cont] * $precio_venta[$cont];

                //Return dd($porcentaje);

                if ($porcentaje == 10) {
                    // Impuesto IVA 10

                    $imp = ((100 + $porcentaje) / $porcentaje);


                    // Monto Gravado del 10 % y Exento
                    $gravada5 = 0;
                    $m_gravada10 = $totalitems;
                    $exenta = 0;


                    // IVA 10 %
                    $iva10 = round($m_gravada10 / $imp);
                    $iva5 = 0;

                    $gravada10 = $m_gravada10 - $iva10;

                    // total
                    $total = $gravada10 + $gravada5 + $exenta + $iva10 + $iva5;

                } else {
                    if ($porcentaje == 5) {
                        // Impuesto IVA 5
                        $imp = ((100 + $porcentaje) / $porcentaje);

                        // Monto Gravado del 5 % y Exento
                        $m_gravada5 = $totalitems;
                        $gravada10 = 0;
                        $exenta = round((($totalitems)) - $m_gravada5);

                        // IVA 5 %
                        $iva10 = 0;
                        $iva5 = round($m_gravada5 / $imp);

                        $gravada5 = $m_gravada5 - $iva5;

                        // total
                        $total = $gravada10 + $gravada5 + $exenta + $iva10 + $iva5;

                    } else {
                        if ($porcentaje == 0) {
                            // Exento

                            // Monto Gravado y Exento
                            $gravada10 = 0;
                            $gravada5 = 0;
                            $exenta = $totalitems;

                            //iva sin IVA                                 
                            $iva10 = 0;
                            $iva5 = 0;

                            //total
                            $total = $gravada10 + $gravada5 + $exenta + $iva10 + $iva5;

                        }
                    }

                }

                $detalle = new NotaCreditoVDetalle();
                $detalle->idnota_creditov = $nota_creditov->idnota_creditov;
                $detalle->idproducto = $idproducto[$cont];
                $detalle->cantidad = $cantidad[$cont];
                $detalle->precio_venta = $precio_venta[$cont];
                $detalle->items = $items;
                $detalle->iva10 = $iva10;
                $detalle->iva5 = $iva5;
                $detalle->gravada10 = $gravada10;
                $detalle->gravada5 = $gravada5;
                $detalle->exenta = $exenta;
                $detalle->totalitems = $totalitems;

                $detalle->save();
                //calcular mi stock

                // === STOCK (DEVOLUCIÃ“N: SUMA) ===
                $pid = (int) $idproducto[$cont];
                $cant = (float) $cantidad[$cont];

                $this->sumarStock(
                    (int) $request->get('idsucursal'),
                    (int) $request->get('iddeposito'),
                    $pid,
                    $cant,
                    (int) $nota_creditov->idnota_creditov,
                    'NC_VENTA',
                    'nota_credito_venta_detalle',
                    'Devolucion por nota de credito venta'
                );


                $cont = $cont + 1;
                $items++;

                $sumiva10 = $sumiva10 + $iva10;
                $sumiva5 = $sumiva5 + $iva5;
                $sumgravada10 = $sumgravada10 + $gravada10;
                $sumgravada5 = $sumgravada5 + $gravada5;
                $sumexenta = $sumexenta + $exenta;
                $sumtotalitems = $sumtotalitems + $totalitems;

            }

            $udpcabecera = DB::table('nota_credito_venta')
                ->where('idnota_creditov', '=', $nota_creditov->idnota_creditov)
                ->update([
                    'totaliva10' => $sumiva10,
                    'totaliva5' => $sumiva5,
                    'totalgravada10' => $sumgravada10,
                    'totalgravada5' => $sumgravada5,
                    'totalexenta' => $sumexenta,
                    'totalventa' => $sumtotalitems
                ]);

            // AJUSTE DE DEUDA (cuenta_cobrar)
            $montoNc = (int) round($sumtotalitems);
            $idventa = (int) $nota_creditov->idventa;
            $idcliente = (int) $nota_creditov->idcliente;

            // baja importe y saldo (nunca menor a 0)
            DB::table('cuenta_cobrar')
                ->where('idventa', $idventa)
                ->update([
                    'importe' => DB::raw("GREATEST(0, importe - {$montoNc})"),
                    'saldo' => DB::raw("GREATEST(0, saldo - {$montoNc})"),
                ]);

            $ventaAuditada = Ventas::findOrFail($idventa);
            $ventaAuditada->saldo_factura = max(0, (int) $ventaAuditada->saldo_factura - $montoNc);
            $ventaAuditada->save();

            DB::table('ventas')
                ->where('idventa', $idventa)
                ->update([
                    'saldo_factura' => $ventaAuditada->saldo_factura,
                    // para que totalventa baje tambiÃ©n, probaremos mas adelante este punto
                    // 'totalventa'    => DB::raw("GREATEST(0, totalventa - {$montoNc})"),
                ]);

            // === DEVOLUCIÃ“N EN CAJA (cobro negativo) ===
            // Solo si la factura ya tiene cobros realizados (o sea, realmente entrÃ³ plata)
            $pagadoReal = $this->pagadoRealVenta($idventa);

            if ($pagadoReal > 0 && $montoNc > 0) {
                $this->registrarDevolucionCajaPorNC(
                    (int) $nota_creditov->idnota_creditov,
                    (int) $idventa,
                    (int) $idcliente,
                    (int) $montoNc,
                    'NC ' . (int) $nota_creditov->idnota_creditov
                );

            }

            $this->recalcularDeudaVenta($idventa);
            $this->sincronizarCobroPendientePorVenta($idventa, $idcliente);

            $hashService = app(LegalDocumentHashService::class);
            $nota_creditov->forceFill([
                'hash_documento' => $hashService->hashNotaCredito((int) $nota_creditov->idnota_creditov),
                'hash_version' => LegalDocumentHashService::VERSION,
            ])->save();

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors($e->getMessage());
        }


        return Redirect::to('ventas/nota_creditov');

    }


    public function show($id)
    {
        [$nota_creditov, $detalles] = $this->datosComprobante((int) $id);
        $hashService = app(LegalDocumentHashService::class);
        $hashValido = $hashService->verificar($nota_creditov->hash_documento ?? null, $hashService->hashNotaCredito((int) $id));

        return view("ventas.nota_creditov.show", ["nota_creditov" => $nota_creditov, "detalles" => $detalles, "hashValido" => $hashValido]);
    }

    public function comprobante($id)
    {
        [$nota_creditov, $detalles] = $this->datosComprobante((int) $id);
        $hashService = app(LegalDocumentHashService::class);
        $hashValido = $hashService->verificar($nota_creditov->hash_documento ?? null, $hashService->hashNotaCredito((int) $id));

        return view("ventas.nota_creditov.comprobante", [
            "nota_creditov" => $nota_creditov,
            "detalles" => $detalles,
            "hashValido" => $hashValido,
        ]);
    }

    public function destroy($id)
    {
        \Log::info('DESTROY NC START', ['id' => $id]);

        DB::beginTransaction();
        try {
            \Log::info('DESTROY TX BEGIN', ['id' => $id]);

            $nc = NotaCreditoV::where('idnota_creditov', (int) $id)
                ->lockForUpdate()
                ->firstOrFail();

            if (in_array(strtoupper(trim((string) $nc->estado)), ['A', 'ANULADO', 'ANULADA'], true)) {
                DB::rollBack(); // porque ya abriste transacciÃ³n
                return Redirect::to('ventas/nota_creditov')
                    ->with('success', 'La nota de crÃ©dito ya esta anulada.');
            }

            \Log::info('DESTROY NC LOADED', [
                'id' => $nc->idnota_creditov,
                'estado' => $nc->estado,
                'idventa' => $nc->idventa,
                'totalventa' => $nc->totalventa,
                'idsucursal' => $nc->idsucursal,
                'iddeposito' => $nc->iddeposito,
            ]);

            // ====== DETALLES
            $detalles = DB::table('nota_credito_venta_detalle')
                ->where('idnota_creditov', (int) $id)
                ->select('idproducto', 'cantidad')
                ->get();

            \Log::info('DESTROY DETALLES', ['count' => $detalles->count()]);

            // ====== STOCK
            foreach ($detalles as $det) {
                $pid = (int) $det->idproducto;
                $cant = (float) $det->cantidad;

                $this->descontarStock(
                    (int) $nc->idsucursal,
                    (int) $nc->iddeposito,
                    $pid,
                    $cant,
                    (int) $nc->idnota_creditov,
                    'NC_VENTA',
                    'nota_credito_venta_detalle',
                    'Reversion por anulacion de nota de credito venta'
                );
            }

            \Log::info('DESTROY STOCK OK');

            // ====== COBROS NC
            $cobrosNc = DB::table('nota_credito_venta_cobro')
                ->where('idnota_creditov', (int) $id)
                ->pluck('id_cobro')
                ->toArray();

            \Log::info('DESTROY COBROS NC', ['count' => count($cobrosNc)]);

            if (!empty($cobrosNc)) {
                Cobro::whereIn('id_cobro', $cobrosNc)->get()->each(function (Cobro $cobro): void {
                    $cobro->cobro_estado = 'Anulado';
                    $cobro->save();
                });

                DB::table('cobros')
                    ->whereIn('id_cobro', $cobrosNc)
                    ->update(['cobro_estado' => 'Anulado']);
            }

            // ====== REVERTIR CUENTA_COBRAR / VENTAS
            $montoNc = (int) abs((int) ($nc->totalventa ?? 0));

            DB::table('cuenta_cobrar')
                ->where('idventa', (int) $nc->idventa)
                ->update(['importe' => DB::raw("importe + {$montoNc}")]);

            \Log::info('DESTROY CUENTA_COBRAR IMPORTE OK', ['montoNc' => $montoNc]);

            // recalculo pagado neto
            $pagadoNeto = $this->pagadoRealVenta((int) $nc->idventa);

            $importeNuevo = (int) DB::table('cuenta_cobrar')
                ->where('idventa', (int) $nc->idventa)
                ->value('importe');

            $saldoNuevo = max(0, $importeNuevo - $pagadoNeto);

            DB::table('cuenta_cobrar')
                ->where('idventa', (int) $nc->idventa)
                ->update(['saldo' => $saldoNuevo]);

            $ventaAuditada = Ventas::findOrFail((int) $nc->idventa);
            $ventaAuditada->saldo_factura = $saldoNuevo;
            $ventaAuditada->save();

            DB::table('ventas')
                ->where('idventa', (int) $nc->idventa)
                ->update(['saldo_factura' => $saldoNuevo]);

            \Log::info('DESTROY SALDOS OK', [
                'pagadoNeto' => $pagadoNeto,
                'importeNuevo' => $importeNuevo,
                'saldoNuevo' => $saldoNuevo
            ]);

            // ====== MARCAR NC ANULADA
            $nc->estado = 'Anulado';
            $nc->hash_anulacion = app(LegalDocumentHashService::class)
                ->hashAnulacion('NOTA_CREDITO_VENTA', (int) $nc->idnota_creditov, request('motivo_anulacion'), Auth::user()->name ?? null);
            $nc->save();

            $this->recalcularDeudaVenta((int) $nc->idventa);
            $this->sincronizarCobroPendientePorVenta((int) $nc->idventa, (int) $nc->idcliente);

            \Log::info('DESTROY NC UPDATED TO ANULADO', ['id' => $nc->idnota_creditov]);

            DB::commit();
            \Log::info('DESTROY COMMIT OK', ['id' => $id]);

            return Redirect::to('ventas/nota_creditov')->with('success', 'Nota de crÃ©dito anulada.');

        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('DESTROY NC ERROR', [
                'id' => $id,
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Redirect::to('ventas/nota_creditov')->with('error', 'Error al anular NC: ' . $e->getMessage());
        }
    }


    public function destroydetalle($id)
    {

        $nota_creditov = NotaCreditoVDetalle::findOrFail($id);
        $idcab = $nota_creditov->idnota_creditov;

        $nota = NotaCreditoV::find($idcab);

        if ($nota && (int) ($nota->totalventa ?? 0) > 0 && ! in_array((string) $nota->estado, self::ESTADOS_ANULADOS, true)) {
            return Redirect::to('ventas/nota_creditov/' . $idcab . '/edit')
                ->with('error', 'No se puede eliminar un detalle de una nota ya aplicada. Ajuste cantidad/precio o anule la nota.');
        }

        $nota_creditov->delete();

        return Redirect::to('ventas/nota_creditov/' . $idcab . '/edit');
    }

    public function insertar_facturas(Request $request)
    {
        $request->validate([
            'idventa' => ['required', 'integer', 'exists:ventas,idventa'],
            'fecha' => ['required', 'date'],
            'concepto' => ['required', 'string', 'max:100'],
        ]);

        return redirect()->route('nota_creditov.create', [
            'idventa' => $request->get('idventa'),
            'fecha' => $request->get('fecha'),
            'concepto' => $request->get('concepto'),
        ]);
    }

    public function edit($id)
    {
        $nota_creditov = DB::table('nota_credito_venta as c')
            ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'c.iddeposito', '=', 'dep.iddeposito')
            ->join('clientes as cli', 'c.idcliente', '=', 'cli.idcliente')
            ->join('users as u', 'c.idusuario', '=', 'u.id')
            ->select('c.idnota_creditov', 'c.nro_nota_credito', 'c.idventa', 'u.name as usuario', 's.idsucursal', 's.descripcion as sucursal', 'dep.iddeposito', 'dep.descripcion as deposito', 'cli.idcliente', 'cli.nombre as cliente', 'cli.num_documento', 'c.fecha_registro', 'c.totaliva10', 'c.totaliva5', 'c.totalgravada10', 'c.totalgravada5', 'c.totalexenta', 'c.totalventa', 'c.timbrado', 'c.condicion', 'c.concepto', 'c.nro_factura', 'c.estado', 'c.fecha_factura')
            ->where('c.idnota_creditov', '=', $id)
            ->orderBy('c.idnota_creditov', 'desc')
            ->first();

        $detalles = DB::table('nota_credito_venta_detalle as d')
            ->join('productos as prod', 'd.idproducto', '=', 'prod.idproducto')
            ->select('d.idnota_creditov_detalle', 'prod.idproducto', 'prod.descripcion as producto', 'd.cantidad', 'd.precio_venta', 'd.iva10', 'd.iva5', 'd.gravada10', 'd.gravada5', 'd.exenta', 'd.totalitems')
            ->where('d.idnota_creditov', '=', $id)
            ->get();

        return view("ventas.nota_creditov.edit", ["nota_creditov" => $nota_creditov, "detalles" => $detalles]);
    }

    public function update(Request $request, $id)
    {
        // ValidaciÃ³n adicional en el lado del servidor
        $request->validate([
            'precio_venta.*' => 'required|numeric|min:1', // Asegura que todos los precios_venta sean numÃ©ricos y mayores a 0
        ]);

        $idnota_creditov_detalle = $request->get('idnota_creditov_detalle');
        $cantidad = $request->get('cantidad');
        $precio_venta = $request->get('precio_venta');
        $idproducto = $request->get('idproducto');

        DB::beginTransaction();

        try {

        // return dd(['a'=>$cantidad,'b'=>$precio_venta,'c'=>$idproducto, 'd'=>$idventa_detalle]);

        $productos = DB::table('productos as prod')
            ->join('tipo_impuesto as ti', 'prod.idtipoimpuesto', '=', 'ti.idtipoimpuesto')
            ->select('prod.idproducto', 'ti.porcentaje')
            ->whereIn('prod.idproducto', $idproducto)
            ->get();

        // return dd(['a'=>$productos,'b'=>$precio_venta,'c'=>$idproducto, 'd'=>$idpresupuestoc_detalle]);
        // ObtÃ©n los valores necesarios de la cabecera de la venta
        $notacreditovCabecera = DB::table('nota_credito_venta')
            ->where('idnota_creditov', '=', $id)
            ->select('idcliente', 'idsucursal', 'fecha_vencimiento', 'fecha_factura', 'idventa', 'iddeposito')
            ->first();

        $oldTotalNcRaw = DB::table('nota_credito_venta')
            ->where('idnota_creditov', $id)
            ->value('totalventa');

        $oldTotalNc = (int) ($oldTotalNcRaw ?? 0);

        // Si aÃºn no tiene total calculado, es la PRIMERA FINALIZACIÃ“N
        $isPrimeraFinalizacion = ($oldTotalNc <= 0);

        // cantidades anteriores por detalle (para delta stock)
        $oldDet = DB::table('nota_credito_venta_detalle')
            ->where('idnota_creditov', $id)
            ->select('idnota_creditov_detalle', 'idproducto', 'cantidad')
            ->get()
            ->keyBy('idnota_creditov_detalle');

        // Verifica si la venta existe antes de continuar       
        $idventa = $notacreditovCabecera->idventa;
        $idcliente = $notacreditovCabecera->idcliente;
        $idsucursal = $notacreditovCabecera->idsucursal;
        $iddeposito = $notacreditovCabecera->iddeposito;
        $fecha_vencimiento = $notacreditovCabecera->fecha_vencimiento;
        $fecha_factura = $notacreditovCabecera->fecha_factura;


        $prod = 0;
        $cont = 0;
        $items = 1;
        $sumiva10 = 0;
        $sumiva5 = 0;
        $sumgravada10 = 0;
        $sumgravada5 = 0;
        $sumexenta = 0;
        $sumtotalitems = 0;

        foreach ($idnota_creditov_detalle as $key => $value) {

            $prod = $productos->where('idproducto', '=', $idproducto[$value])->first();
            // return dd($prod);
            //  calculo de los demas campos que estÃ¡ en el notepad
            $porcentaje = $prod->porcentaje;
            $totalitems = 0;
            $totalitems = $cantidad[$value] * $precio_venta[$value];
            // return dd(['a'=>$cantidad[$value],'b'=>$precio_venta[$value],'c'=>$totalitems]);

            if ($porcentaje == 10) {
                // Impuesto IVA 10

                $imp = ((100 + $porcentaje) / $porcentaje);


                // Monto Gravado del 10 % y Exento
                $gravada5 = 0;
                $m_gravada10 = $totalitems;
                $exenta = 0;


                // IVA 10 %
                $iva10 = round($m_gravada10 / $imp);
                $iva5 = 0;

                $gravada10 = $m_gravada10 - $iva10;

                // total
                $total = $gravada10 + $gravada5 + $exenta + $iva10 + $iva5;

            } else {
                if ($porcentaje == 5) {
                    // Impuesto IVA 5
                    $imp = ((100 + $porcentaje) / $porcentaje);

                    // Monto Gravado del 5 % y Exento
                    $m_gravada5 = $totalitems;
                    $gravada10 = 0;
                    $exenta = round((($totalitems)) - $m_gravada5);

                    // IVA 5 %
                    $iva10 = 0;
                    $iva5 = round($m_gravada5 / $imp);

                    $gravada5 = $m_gravada5 - $iva5;

                    // total
                    $total = $gravada10 + $gravada5 + $exenta + $iva10 + $iva5;

                } else {
                    if ($porcentaje == 0) {
                        // Exento

                        // Monto Gravado y Exento
                        $gravada10 = 0;
                        $gravada5 = 0;
                        $exenta = $totalitems;

                        //iva sin IVA                                 
                        $iva10 = 0;
                        $iva5 = 0;

                        //total
                        $total = $gravada10 + $gravada5 + $exenta + $iva10 + $iva5;

                    }
                }

            }
            $udpdetalle = DB::table('nota_credito_venta_detalle')
                ->where('idnota_creditov_detalle', '=', $value)
                ->update([
                    'cantidad' => $cantidad[$value],
                    'precio_venta' => $precio_venta[$value],
                    'iva10' => $iva10,
                    'iva5' => $iva5,
                    'gravada10' => $gravada10,
                    'gravada5' => $gravada5,
                    'exenta' => $exenta,
                    'totalitems' => $totalitems
                ]);

            $pid = (int) $idproducto[$value];
            $newCant = (float) $cantidad[$value];

            // cantidad anterior
            $oldCant = 0;

            if (!$isPrimeraFinalizacion && isset($oldDet[$value])) {
                $oldCant = (float) $oldDet[$value]->cantidad;
            }

            // delta: si sube, suma stock; si baja, resta stock
            $deltaQty = $newCant - $oldCant;

            if ($deltaQty != 0) {
                if ($deltaQty > 0) {
                    $this->sumarStock(
                        (int) $idsucursal,
                        (int) $iddeposito,
                        $pid,
                        (float) $deltaQty,
                        (int) $id,
                        'NC_VENTA',
                        'nota_credito_venta_detalle',
                        'Ajuste de stock por modificacion de nota de credito venta'
                    );
                } else {
                    $abs = abs($deltaQty);
                    $this->descontarStock(
                        (int) $idsucursal,
                        (int) $iddeposito,
                        $pid,
                        (float) $abs,
                        (int) $id,
                        'NC_VENTA',
                        'nota_credito_venta_detalle',
                        'Reversion parcial por modificacion de nota de credito venta'
                    );
                }
            }


            $cont = $cont + 1;
            $items++;

            $sumiva10 = $sumiva10 + $iva10;
            $sumiva5 = $sumiva5 + $iva5;
            $sumgravada10 = $sumgravada10 + $gravada10;
            $sumgravada5 = $sumgravada5 + $gravada5;
            $sumexenta = $sumexenta + $exenta;
            $sumtotalitems = $sumtotalitems + $totalitems;

        } //fin foreach

        $udpcabecera = DB::table('nota_credito_venta')
            ->where('idnota_creditov', '=', $id)
            ->update([
                'totaliva10' => $sumiva10,
                'totaliva5' => $sumiva5,
                'totalgravada10' => $sumgravada10,
                'totalgravada5' => $sumgravada5,
                'totalexenta' => $sumexenta,
                'totalventa' => $sumtotalitems
            ]);

        // === DELTA total NC ===
        $newTotalNc = (int) round($sumtotalitems);
        $deltaTotal = $newTotalNc - $oldTotalNc; // + => aumentÃ³ NC (mÃ¡s devoluciÃ³n); - => bajÃ³ NC

        if ($deltaTotal != 0) {

            // Ajuste deuda: NC reduce deuda => restamos delta sobre importe/saldo
            DB::table('cuenta_cobrar')
                ->where('idventa', (int) $idventa)
                ->update([
                    'importe' => DB::raw("GREATEST(0, importe - ({$deltaTotal}))"),
                    'saldo' => DB::raw("GREATEST(0, saldo - ({$deltaTotal}))"),
                ]);

            $ventaAuditada = Ventas::findOrFail((int) $idventa);
            $ventaAuditada->saldo_factura = max(0, (int) $ventaAuditada->saldo_factura - (int) $deltaTotal);
            $ventaAuditada->save();

            DB::table('ventas')
                ->where('idventa', (int) $idventa)
                ->update([
                    'saldo_factura' => $ventaAuditada->saldo_factura,
                    // opcional: si querÃ©s que totalventa baje tambiÃ©n:
                    // 'totalventa'    => DB::raw("GREATEST(0, totalventa - ({$deltaTotal}))"),
                ]);

            // Ajuste caja: si deltaTotal > 0 => devolvÃ©s mÃ¡s (negativo).
            // si deltaTotal < 0 => devolviste de mÃ¡s antes, entonces debe re-ingresar (positivo).
            $pagadoReal = $this->pagadoRealVenta((int) $idventa);

            if ($pagadoReal > 0) {
                if ($deltaTotal > 0) {
                    $this->registrarDevolucionCajaPorNC(
                        (int) $id,
                        (int) $idventa,
                        (int) $idcliente,
                        (int) $deltaTotal,
                        'AJUSTE NC ' . (int) $id
                    );

                } elseif ($deltaTotal < 0) {
                    $abs = abs($deltaTotal);

                    $this->registrarReingresoCajaPorNC(
                        (int) $id,
                        (int) $idventa,
                        (int) $idcliente,
                        (int) $abs,
                        'REING AJUSTE NC ' . (int) $id
                    );
                }
            }
        }

        // NO fuerces ventas.estado = 'F' acÃ¡. Eso te rompe el flujo de estados.
        // Si querÃ©s, podÃ©s setear estado segÃºn saldo_factura, pero no invento tu regla.


        $this->recalcularDeudaVenta((int) $idventa);
        $this->sincronizarCobroPendientePorVenta((int) $idventa, (int) $idcliente);

        $notaActualizada = NotaCreditoV::findOrFail((int) $id);
        $hashService = app(LegalDocumentHashService::class);
        $notaActualizada->forceFill([
            'hash_documento' => $hashService->hashNotaCredito((int) $id),
            'hash_version' => LegalDocumentHashService::VERSION,
        ])->save();

        DB::commit();

        return Redirect::to('ventas/nota_creditov/' . $id);

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->withErrors('Error al actualizar nota de credito: ' . $e->getMessage());
        }

    }

    private function sumarStock(
        int $idsucursal,
        int $iddeposito,
        int $idproducto,
        float $cantidad,
        ?int $idOrigen = null,
        ?string $tipoOrigen = null,
        ?string $detalleOrigen = null,
        ?string $observacion = null
    ): void
    {
        $baseStock = DB::table('stock')
            ->where('idsucursal', $idsucursal)
            ->where('iddeposito', $iddeposito)
            ->where('idproducto', $idproducto);

        $updated = (clone $baseStock)->increment('cantidad', $cantidad);

        if ($updated === 0) {
            DB::table('stock')->insert([
                'idsucursal' => $idsucursal,
                'iddeposito' => $iddeposito,
                'idproducto' => $idproducto,
                'cantidad' => $cantidad,
            ]);
        }

        if ($idOrigen !== null && $tipoOrigen !== null) {
            app(MovimientoStockService::class)->registrar(
                $idproducto,
                $idsucursal,
                $iddeposito,
                $tipoOrigen,
                $idOrigen,
                $detalleOrigen,
                'ENTRADA',
                $cantidad,
                null,
                $observacion
            );
        }
    }

    private function descontarStock(
        int $idsucursal,
        int $iddeposito,
        int $idproducto,
        float $cantidad,
        ?int $idOrigen = null,
        ?string $tipoOrigen = null,
        ?string $detalleOrigen = null,
        ?string $observacion = null
    ): void
    {
        $stock = DB::table('stock')
            ->where('idsucursal', $idsucursal)
            ->where('iddeposito', $iddeposito)
            ->where('idproducto', $idproducto)
            ->lockForUpdate()
            ->first();

        if (! $stock || (float) $stock->cantidad < $cantidad) {
            $producto = DB::table('productos')->where('idproducto', $idproducto)->value('descripcion') ?? $idproducto;
            $disponible = $stock ? (float) $stock->cantidad : 0;

            throw new \RuntimeException("Stock insuficiente para {$producto}. Disponible: {$disponible}, requerido: {$cantidad}.");
        }

        DB::table('stock')
            ->where('idsucursal', $idsucursal)
            ->where('iddeposito', $iddeposito)
            ->where('idproducto', $idproducto)
            ->decrement('cantidad', $cantidad);

        if ($idOrigen !== null && $tipoOrigen !== null) {
            app(MovimientoStockService::class)->registrar(
                $idproducto,
                $idsucursal,
                $iddeposito,
                $tipoOrigen,
                $idOrigen,
                $detalleOrigen,
                'SALIDA',
                $cantidad,
                null,
                $observacion
            );
        }
    }

    private function datosComprobante(int $id): array
    {
        $nota_creditov = DB::table('nota_credito_venta as c')
            ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'c.iddeposito', '=', 'dep.iddeposito')
            ->join('clientes as cli', 'c.idcliente', '=', 'cli.idcliente')
            ->leftJoin('ventas as v', 'c.idventa', '=', 'v.idventa')
            ->join('users as u', 'c.idusuario', '=', 'u.id')
            ->select(
                'c.idnota_creditov',
                'c.nro_nota_credito',
                'c.idventa',
                'u.name as usuario',
                's.idsucursal',
                's.descripcion as sucursal',
                'dep.iddeposito',
                'dep.descripcion as deposito',
                'cli.idcliente',
                'cli.nombre as cliente',
                'cli.num_documento',
                'c.fecha_registro',
                'c.totaliva10',
                'c.totaliva5',
                'c.totalgravada10',
                'c.totalgravada5',
                'c.totalexenta',
                'c.totalventa',
                'c.timbrado',
                'c.condicion',
                'c.concepto',
                'c.nro_factura',
                'c.estado',
                'c.fecha_factura',
                'c.hash_documento',
                'c.hash_anulacion',
                'c.hash_version',
                'v.nro_factura as factura_afectada'
            )
            ->where('c.idnota_creditov', '=', $id)
            ->orderBy('c.idnota_creditov', 'desc')
            ->first();

        if (!$nota_creditov) {
            abort(404, 'Nota de credito no encontrada.');
        }

        $detalles = DB::table('nota_credito_venta_detalle as d')
            ->join('productos as prod', 'd.idproducto', '=', 'prod.idproducto')
            ->select(
                'prod.descripcion as producto',
                'd.cantidad',
                'd.precio_venta',
                'd.iva10',
                'd.iva5',
                'd.gravada10',
                'd.gravada5',
                'd.exenta',
                'd.totalitems'
            )
            ->where('d.idnota_creditov', '=', $id)
            ->orderBy('d.items')
            ->get();

        return [$nota_creditov, $detalles];
    }

    private function timbradoActivo(int $idsucursal): ?object
    {
        return DB::table('timbrado')
            ->where('idsucursal', $idsucursal)
            ->where('estado', 'Activo')
            ->whereDate('fecha_vencimiento', '>=', now()->toDateString())
            ->orderByDesc('idtimbrado')
            ->first();
    }

    private function generarNumeroNota(int $idnota, ?string $serie): string
    {
        $serie = $serie ?: '001-001';

        return $serie . '-' . str_pad((string) $idnota, 7, '0', STR_PAD_LEFT);
    }

    private function proximoIdTabla(string $tabla, string $pk): int
    {
        return ((int) DB::table($tabla)->max($pk)) + 1;
    }

    private function pagadoRealVenta(int $idventa): int
    {
        return (int) DB::table('det_cobro as dc')
            ->join('cobros as c', 'dc.id_cobro', '=', 'c.id_cobro')
            ->leftJoin('nota_credito_venta_cobro as ncc', 'c.id_cobro', '=', 'ncc.id_cobro')
            ->where('dc.idventa', $idventa)
            ->where('c.cobro_estado', 'Realizado')
            ->whereNull('ncc.id_cobro')
            ->whereNotExists(function ($sub) {
                $sub->selectRaw('1')
                    ->from('det_formacobro as df_nc')
                    ->whereColumn('df_nc.id_cobro', 'c.id_cobro')
                    ->where(function ($q) {
                        $q->where('df_nc.documento', 'LIKE', 'NC %')
                            ->orWhere('df_nc.documento', 'LIKE', 'AJUSTE NC %')
                            ->orWhere('df_nc.documento', 'LIKE', 'REING NC %')
                            ->orWhere('df_nc.documento', 'LIKE', 'REING AJUSTE NC %')
                            ->orWhere('df_nc.documento', 'LIKE', 'REINGRESO AJUSTE NC %');
                    });
            })
            ->sum('dc.monto_detcobro');
    }

    private function recalcularDeudaVenta(int $idventa): void
    {
        $venta = Ventas::where('idventa', $idventa)->lockForUpdate()->firstOrFail();
        $importe = $this->importeVentaAjustado($idventa, (int) $venta->totalventa);

        $pagadoReal = $this->pagadoRealVenta($idventa);

        $pagadoReal = min(max(0, $pagadoReal), $importe);
        $saldo = max(0, $importe - $pagadoReal);
        $estado = $saldo === 0 ? 'Pagado' : ($pagadoReal > 0 ? 'Parcial' : 'Generado');

        $cta = DB::table('cuenta_cobrar')
            ->where('idventa', $idventa)
            ->lockForUpdate()
            ->first();

        if (! $cta) {
            DB::table('cuenta_cobrar')->insert([
                'idcliente' => $venta->idcliente,
                'fecha' => $venta->fecha ?? now()->toDateString(),
                'obs' => 'Generado por nota de credito de venta',
                'estado' => $estado,
                'monto_pago' => $pagadoReal,
                'saldo' => $saldo,
                'idventa' => $idventa,
                'condicion' => $venta->condicion,
                'importe' => $importe,
                'fecha_vencimiento' => $venta->fecha ?? now()->toDateString(),
            ]);
        } else {
            DB::table('cuenta_cobrar')
                ->where('idcuenta_cobrar', $cta->idcuenta_cobrar)
                ->update([
                    'importe' => $importe,
                    'monto_pago' => $pagadoReal,
                    'saldo' => $saldo,
                    'estado' => $estado,
                ]);
        }

        $venta->saldo_factura = $saldo;
        $venta->estado = $saldo === 0 ? 'Realizado' : 'Pendiente';
        $venta->save();
    }

    private function importeVentaAjustado(int $idventa, int $totalVenta): int
    {
        $totalCredito = (int) DB::table('nota_credito_venta')
            ->where('idventa', $idventa)
            ->whereNotIn('estado', self::ESTADOS_ANULADOS)
            ->sum('totalventa');

        $totalDebito = (int) DB::table('nota_debito_venta')
            ->where('idventa', $idventa)
            ->whereNotIn('estado', self::ESTADOS_ANULADOS)
            ->sum('totalventa');

        return max(0, $totalVenta + $totalDebito - $totalCredito);
    }

    private function sincronizarCobroPendientePorVenta(int $idventa, int $idcliente): void
    {
        $cta = DB::table('cuenta_cobrar')
            ->where('idventa', $idventa)
            ->lockForUpdate()
            ->first();

        if (! $cta) {
            return;
        }

        $saldo = (int) $cta->saldo;
        $pendiente = DB::table('det_cobro as dc')
            ->join('cobros as c', 'dc.id_cobro', '=', 'c.id_cobro')
            ->where('dc.idventa', $idventa)
            ->where('c.cobro_estado', 'Pendiente')
            ->select('c.id_cobro', 'dc.id_detcobro')
            ->orderByDesc('c.id_cobro')
            ->first();

        if ($saldo <= 0) {
            if ($pendiente) {
                $cobroPendiente = Cobro::findOrFail($pendiente->id_cobro);
                $cobroPendiente->cobro_estado = 'Anulado';
                $cobroPendiente->idusuario = Auth::id();
                $cobroPendiente->save();
            }

            return;
        }

        if ($pendiente) {
            $cobroPendiente = Cobro::findOrFail($pendiente->id_cobro);
            $cobroPendiente->monto_cobro = $saldo;
            $cobroPendiente->idusuario = Auth::id();
            $cobroPendiente->save();

            $detallePendiente = CobroDetalle::findOrFail($pendiente->id_detcobro);
            $detallePendiente->monto_detcobro = $saldo;
            $detallePendiente->save();

            return;
        }

        $apertura = DB::table('apertura')
            ->where('idsucursal', (int) Auth::user()->trabaja_sucursal)
            ->where('estado', 'Abierto')
            ->orderByDesc('idapertura')
            ->first();

        if (! $apertura) {
            throw new \RuntimeException('La nota de credito deja saldo pendiente. Debe existir una caja abierta para crear el cobro pendiente.');
        }

        $cobro = Cobro::create([
            'idcaja' => $apertura->idcaja,
            'idsucursal' => $apertura->idsucursal,
            'idapertura' => $apertura->idapertura,
            'fecha_cobro' => now()->toDateString(),
            'idcliente' => $idcliente,
            'monto_cobro' => $saldo,
            'cobro_estado' => 'Pendiente',
            'idusuario' => Auth::id(),
        ]);

        CobroDetalle::create([
            'id_cobro' => $cobro->id_cobro,
            'items' => 1,
            'idventa' => $idventa,
            'monto_detcobro' => $saldo,
        ]);
    }

    private function registrarDevolucionCajaPorNC(int $idnota_creditov, int $idventa, int $idcliente, int $monto, string $ref = '')
    {
        // monto debe venir POSITIVO, acÃ¡ lo registramos NEGATIVO
        if ($monto <= 0)
            return;

        $user = Auth::user();
        $idsucursal = (int) $user->trabaja_sucursal;

        // Buscar apertura ABIERTA del usuario (mejor que por sucursal solo)
        $apertura = DB::table('apertura')
            ->where('id', $user->id)
            ->where('idsucursal', $idsucursal)
            ->where('estado', 'Abierto')
            ->orderByDesc('idapertura')
            ->first();

        if (!$apertura) {
            throw new \Exception('No hay caja ABIERTA para registrar la devoluciÃ³n (nota de crÃ©dito).');
        }

        // Forma de cobro para devoluciÃ³n: intenta "Efectivo" por defecto
        $idForma = DB::table('formacobro')
            ->whereRaw("LOWER(TRIM(descripcion)) = 'efectivo'")
            ->value('id_formacobro');

        if (!$idForma) {
            // Si no existe exacto "efectivo", toma el primero como fallback
            $idForma = DB::table('formacobro')->min('id_formacobro');
        }

        if (!$idForma) {
            throw new \Exception("No existe ninguna forma de cobro en la tabla formacobro.");
        }

        $fecha = Carbon::now('America/Asuncion')->toDateString();
        $montoNeg = -1 * (int) $monto;

        // CABECERA COBRO (NEGATIVO)
        $cobroDevolucion = Cobro::create([
            'idsucursal' => (int) $apertura->idsucursal,
            'idcaja' => (int) $apertura->idcaja,
            'idapertura' => (int) $apertura->idapertura,
            'fecha_cobro' => $fecha,
            'monto_cobro' => $montoNeg,
            'cobro_estado' => 'Realizado', // NO lo pongas "Anulado" porque el arqueo lo ignora
            'idusuario' => $user->id,
            'idcliente' => (int) $idcliente,
        ]);
        $idCobroDev = $cobroDevolucion->id_cobro;

        // DETALLE COBRO (NEGATIVO) - vincula la venta
        CobroDetalle::create([
            'id_cobro' => (int) $idCobroDev,
            'idventa' => (int) $idventa,
            'items' => 1,
            'monto_detcobro' => (int) $montoNeg,
        ]);

        // DETALLE FORMA COBRO (NEGATIVO)
        FormaCobroDetalle::create([
            'id_cobro' => (int) $idCobroDev,
            'id_formacobro' => (int) $idForma,
            'items' => 1,
            'identidademisora' => null,
            'monto_detformacobro' => (int) $montoNeg,
            'monto_recibido' => null,
            'vuelto' => null,
            'documento' => $ref ?: ('NC DEV ' . $idventa),
            'fecha' => $fecha,
            'fecha_vencimiento' => null,
            'idtipodocumento' => null,
        ]);

        // ===== MAPEAR COBRO CON LA NC =====
        DB::table('nota_credito_venta_cobro')->insert([
            'idnota_creditov' => (int) $idnota_creditov,
            'id_cobro' => (int) $idCobroDev,
            'tipo' => 'DEV',
        ]);
    }

    private function registrarReingresoCajaPorNC(int $idnota_creditov, int $idventa, int $idcliente, int $monto, string $ref = '')
    {
        if ($monto <= 0)
            return;

        $user = Auth::user();
        $idsucursal = (int) $user->trabaja_sucursal;

        $apertura = DB::table('apertura')
            ->where('id', $user->id)
            ->where('idsucursal', $idsucursal)
            ->where('estado', 'Abierto')
            ->orderByDesc('idapertura')
            ->first();

        if (!$apertura) {
            throw new \Exception('No hay caja ABIERTA para registrar el reingreso del ajuste de NC.');
        }

        $idForma = DB::table('formacobro')
            ->whereRaw("LOWER(TRIM(descripcion)) = 'efectivo'")
            ->value('id_formacobro');

        if (!$idForma)
            $idForma = DB::table('formacobro')->min('id_formacobro');
        if (!$idForma)
            throw new \Exception("No existe ninguna forma de cobro en formacobro.");

        $fecha = Carbon::now('America/Asuncion')->toDateString();
        $montoPos = (int) $monto;

        $cobroIngreso = Cobro::create([
            'idsucursal' => (int) $apertura->idsucursal,
            'idcaja' => (int) $apertura->idcaja,
            'idapertura' => (int) $apertura->idapertura,
            'fecha_cobro' => $fecha,
            'monto_cobro' => $montoPos,
            'cobro_estado' => 'Realizado',
            'idusuario' => $user->id,
            'idcliente' => (int) $idcliente,
        ]);
        $idCobroIng = $cobroIngreso->id_cobro;

        CobroDetalle::create([
            'id_cobro' => (int) $idCobroIng,
            'idventa' => (int) $idventa,
            'items' => 1,
            'monto_detcobro' => (int) $montoPos,
        ]);

        FormaCobroDetalle::create([
            'id_cobro' => (int) $idCobroIng,
            'id_formacobro' => (int) $idForma,
            'items' => 1,
            'identidademisora' => null,
            'monto_detformacobro' => (int) $montoPos,
            'monto_recibido' => null,
            'vuelto' => null,
            'documento' => $ref ?: ('REING NC ' . $idventa),
            'fecha' => $fecha,
            'fecha_vencimiento' => null,
            'idtipodocumento' => null,
        ]);

        DB::table('nota_credito_venta_cobro')->insert([
            'idnota_creditov' => (int) $idnota_creditov,
            'id_cobro' => (int) $idCobroIng,
            'tipo' => 'REING',
        ]);
    }

}
