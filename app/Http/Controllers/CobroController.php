<?php

namespace App\Http\Controllers;

use App\Models\Cobro;
use App\Models\CobroDetalle;
use App\Models\FormaCobroDetalle;
use App\Models\Ventas;
use App\Services\LegalDocumentHashService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class CobroController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // LISTADO
    public function index(Request $request)
    {
        $q1 = $request->get('searchText');   // id
        $q2 = $request->get('searchText2');  // fecha
        $q3 = $request->get('searchText3');  // estado
        $q4 = $request->get('searchText4');  // sucursal
        $q5 = $request->get('searchText5');  // razon social
        $q6 = $request->get('searchText6');  // doc

        $cobros = DB::table('cobros as c')
            ->join('apertura as a', 'c.idapertura', '=', 'a.idapertura')
            ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
            ->join('clientes as cli', 'c.idcliente', '=', 'cli.idcliente')
            ->leftJoin('det_cobro as dc', 'c.id_cobro', '=', 'dc.id_cobro')
            ->leftJoin('ventas as v', 'dc.idventa', '=', 'v.idventa')
            ->leftJoin('cuenta_cobrar as cc', 'dc.idventa', '=', 'cc.idventa')
            ->leftJoin('nota_credito_venta_cobro as ncc', 'c.id_cobro', '=', 'ncc.id_cobro')
            ->leftJoin('venta_credito_aceptaciones as vca', function ($join) {
                $join->on('vca.idventa', '=', 'v.idventa')
                    ->where('vca.estado', '=', 'Aceptado');
            })
            ->select(
                'c.id_cobro',
                'c.fecha_cobro',
                'c.monto_cobro',
                'c.cobro_estado',
                'c.usuario',
                's.descripcion as sucursal',
                'c.idapertura',
                'cli.nombre as cliente',
                'cli.num_documento',
                DB::raw('MIN(dc.idventa) as idventa'),
                DB::raw("MAX(CASE WHEN LOWER(TRIM(COALESCE(v.condicion, ''))) <> 'contado' AND v.idventa IS NOT NULL AND vca.idaceptacion_credito IS NULL THEN 1 ELSE 0 END) as credito_pendiente_aceptacion"),
                DB::raw('MAX(cc.saldo) as saldo_cuenta'),
                DB::raw('MAX(cc.fecha_vencimiento) as fecha_vencimiento_cuenta')
            )
            ->when($q1, fn($x) => $x->where('c.id_cobro', 'LIKE', "%$q1%"))
            ->when($q2, fn($x) => $x->whereDate('c.fecha_cobro', $q2))
            ->when($q3, fn($x) => $x->where('c.cobro_estado', 'LIKE', "%$q3%"))
            ->when($q4, fn($x) => $x->where('s.descripcion', 'LIKE', "%$q4%"))
            ->when($q5, fn($x) => $x->where('cli.nombre', 'LIKE', "%$q5%"))
            ->when($q6, fn($x) => $x->where('cli.num_documento', 'LIKE', "%$q6%"))
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
            ->groupBy(
                'c.id_cobro',
                'c.fecha_cobro',
                'c.monto_cobro',
                'c.cobro_estado',
                'c.usuario',
                's.descripcion',
                'c.idapertura',
                'cli.nombre',
                'cli.num_documento'
            )
            ->orderByDesc('c.id_cobro')
            ->paginate(10);

        return view('ventas.cobro.index', compact('cobros', 'q1', 'q2', 'q3', 'q4', 'q5', 'q6'));
    }

    public function create()
    {
        return redirect()->route('cobro.index');
    }

    public function cobrarfactura(Request $request, $id)
    {
        $venta = Ventas::findOrFail($id);

        if (in_array($venta->estado, ['Cancelado', 'Anulado'])) {
            return redirect()->back()->with('error', 'La venta está anulada/cancelada.');
        }

        if ($this->ventaCreditoSinAceptar((int) $venta->idventa)) {
            return redirect()
                ->route('venta.show', $venta->idventa)
                ->with('error', 'Antes de cobrar una venta a credito debe registrar la firma fisica del compromiso de pago.');
        }

        $user = Auth::user();
        $idsucursal = (int) $user->trabaja_sucursal;

        $apertura = DB::table('apertura')
            ->where('idsucursal', $idsucursal)
            ->where('estado', 'Abierto')
            ->orderByDesc('idapertura')
            ->first();

        if (!$apertura) {
            return redirect()->back()->with('error', 'No hay una apertura de caja ABIERTA en tu sucursal. Abrí caja primero.');
        }

        // Si existe cuenta a cobrar, cobrás el saldo (no el totalventa)
        $cta = DB::table('cuenta_cobrar')->where('idventa', $venta->idventa)->first();
        if ($cta && (int) $cta->saldo <= 0) {
            return redirect()->back()->with('error', 'Esta factura ya está saldada.');
        }

        $monto = $cta ? (int) $cta->saldo : (int) $venta->totalventa;

        $pendiente = DB::table('det_cobro as dc')
        ->join('cobros as c','dc.id_cobro','=','c.id_cobro')
        ->where('dc.idventa', $venta->idventa)
        ->where('c.cobro_estado','Pendiente')
        ->orderByDesc('c.id_cobro')
        ->select('c.id_cobro')
        ->first();

        if ($pendiente) {
        return redirect()->route('cobro.edit', $pendiente->id_cobro);
        }

        DB::beginTransaction();
        try {
            $cobro = Cobro::create([
                'idcaja' => $apertura->idcaja,
                'idsucursal' => $apertura->idsucursal,
                'idapertura' => $apertura->idapertura,
                'fecha_cobro' => now()->toDateString(),
                'idcliente' => $venta->idcliente,
                'monto_cobro' => $monto,
                'cobro_estado' => 'Pendiente',
                'usuario' => $user->name,
            ]);

            CobroDetalle::create([
                'id_cobro' => $cobro->id_cobro,
                'items' => 1,
                'idventa' => $venta->idventa,
                'monto_detcobro' => $monto,
            ]);

            DB::commit();
            return redirect()->route('cobro.edit', $cobro->id_cobro);

        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al generar cobro: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $cobros = DB::table('cobros as c')
            ->join('apertura as a', 'c.idapertura', '=', 'a.idapertura')
            ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
            ->join('clientes as cli', 'c.idcliente', '=', 'cli.idcliente')
            ->join('cajas as ca', 'c.idcaja', '=', 'ca.idcaja')
            ->select(
                'c.id_cobro',
                'c.fecha_cobro',
                'c.monto_cobro',
                'c.cobro_estado',
                'c.usuario',
                's.idsucursal',
                's.descripcion as sucursal',
                'cli.idcliente',
                'cli.nombre as cliente',
                'cli.num_documento',
                'ca.idcaja',
                'ca.descripcion as caja',
                'c.idapertura'
            )
            ->where('c.id_cobro', $id)
            ->first();

        if (!$cobros) {
            return redirect()->route('cobro.index')->with('error', 'Cobro no encontrado.');
        }

        if ($cobros->cobro_estado !== 'Pendiente') {
            return redirect()
                ->route('cobro.show', $id)
                ->with('info', 'Este cobro ya no puede ser modificado porque no está en estado Pendiente.');
        }

        $cobrodetalle = DB::table('det_cobro as d')
            ->join('ventas as v', 'd.idventa', '=', 'v.idventa')
            ->select('d.id_detcobro', 'd.id_cobro', 'd.items', 'd.monto_detcobro', 'v.idventa', 'v.nro_factura', 'v.condicion', 'v.totalventa')
            ->where('d.id_cobro', $id)
            ->orderBy('d.items')
            ->get();

        foreach ($cobrodetalle as $detalle) {
            if ($this->ventaCreditoSinAceptar((int) $detalle->idventa)) {
                return redirect()
                    ->route('venta.show', $detalle->idventa)
                    ->with('error', 'Este cobro corresponde a una venta a credito sin firma fisica registrada.');
            }
        }

        $forma_cobro = DB::table('formacobro')
            ->select('id_formacobro', 'descripcion', 'requiere_banco', 'requiere_documento', 'requiere_vencimiento')
            ->orderBy('id_formacobro')
            ->get();

        $banco = DB::table('entidademisora')->orderBy('descripcion')->get();

        $pagos = DB::table('det_formacobro as d')
            ->leftJoin('formacobro as fc', 'd.id_formacobro', '=', 'fc.id_formacobro')
            ->leftJoin('entidademisora as e', 'd.identidademisora', '=', 'e.identidademisora')
            ->select(
                'd.id_detformacobro',
                'd.items',
                'fc.descripcion as formacobro',
                'e.descripcion as banco',
                'd.documento',
                'd.fecha',
                'd.fecha_vencimiento',
                'd.monto_detformacobro',
                'd.monto_recibido',
                'd.vuelto'
            )
            ->where('d.id_cobro', $id)
            ->orderBy('d.items')
            ->get();

        $totalPagado = (int) DB::table('det_formacobro')
            ->where('id_cobro', $id)
            ->sum('monto_detformacobro');

        // CONTADO => NO parcial. CRÉDITO => permite parcial.
        $cond = mb_strtolower(trim((string)($cobrodetalle->first()->condicion ?? '')));
        $allowPartial = ($cond !== 'contado');

        return view('ventas.cobro.edit', compact('cobros', 'cobrodetalle', 'forma_cobro', 'banco', 'pagos', 'totalPagado', 'allowPartial'));
    }

    public function finalizar(Request $request, $id_cobro)
    {
        DB::beginTransaction();
        try {
            $ids = $request->input('id_formacobro', []);
            $bancos = $request->input('identidademisora', []);
            $documentos = $request->input('documento', []);
            $fechas = $request->input('fecha', []);
            $vencs = $request->input('fecha_vencimiento', []);
            $montos = $request->input('monto', []);
            $recibidos = $request->input('monto_recibido', []);
            $vueltos = $request->input('vuelto', []);

            if (count($ids) === 0) {
                DB::rollBack();
                return back()->with('error', 'Debés cargar al menos una forma de pago.');
            }

            $cobro = Cobro::where('id_cobro', $id_cobro)->lockForUpdate()->first();
            if (!$cobro) {
                DB::rollBack();
                return back()->with('error', 'Cobro no encontrado.');
            }

            if ($cobro->cobro_estado !== 'Pendiente') {
                DB::rollBack();
                return back()->with('error', 'Solo se pueden finalizar cobros en estado Pendiente.');
            }

            // condición (si hay alguna venta CONTADO => NO parcial)
            if ($this->cobroTieneCreditoSinAceptar((int) $id_cobro)) {
                DB::rollBack();
                return back()->with('error', 'No se puede finalizar el cobro: existe una venta a credito sin respaldo de firma fisica.');
            }

            $condiciones = DB::table('det_cobro as dc')
                ->join('ventas as v', 'dc.idventa', '=', 'v.idventa')
                ->where('dc.id_cobro', $id_cobro)
                ->pluck('v.condicion');

            $hayContado = $condiciones->map(fn($x) => mb_strtolower(trim((string) $x)))->contains('contado');
            $allowPartial = !$hayContado;

            // Reintento: borrá y reinsertá TODO lo enviado
            DB::table('det_formacobro')->where('id_cobro', $id_cobro)->delete();

            $montoCobroOriginal = (int) $cobro->monto_cobro;
            $totalImputado = 0;

            for ($i = 0; $i < count($ids); $i++) {
                $formaId = (int) $ids[$i];

                $forma = DB::table('formacobro')->where('id_formacobro', $formaId)->first();
                if (!$forma) {
                    DB::rollBack();
                    return back()->with('error', "Forma de cobro inválida (ID: $formaId)");
                }

                $banco = $bancos[$i] ?? null;
                $doc = $documentos[$i] ?? null;
                $fecha = $fechas[$i] ?? now()->toDateString();
                $venc = $vencs[$i] ?? null;

                if ((int) $forma->requiere_banco === 1 && empty($banco)) {
                    DB::rollBack();
                    return back()->with('error', "La forma '{$forma->descripcion}' requiere banco.");
                }
                if ((int) $forma->requiere_documento === 1 && empty($doc)) {
                    DB::rollBack();
                    return back()->with('error', "La forma '{$forma->descripcion}' requiere documento.");
                }
                if ((int) $forma->requiere_vencimiento === 1 && empty($venc)) {
                    DB::rollBack();
                    return back()->with('error', "La forma '{$forma->descripcion}' requiere fecha de vencimiento.");
                }

                $desc = mb_strtolower(trim((string) $forma->descripcion));
                $esEfectivo = ($desc === 'efectivo');

                $restante = $montoCobroOriginal - $totalImputado;
                if ($restante <= 0) {
                    DB::rollBack();
                    return back()->with('error', 'El cobro ya está completo. No se pueden agregar más pagos.');
                }

                $m = (int) ($montos[$i] ?? 0);

                if ($esEfectivo) {
                    // EFECTIVO: recibido puede ser mayor (genera vuelto)
                    $montoRecibido = (int) ($recibidos[$i] ?? 0);
                    if ($montoRecibido <= 0) {
                        DB::rollBack();
                        return back()->with('error', "En EFECTIVO debés cargar el monto recibido.");
                    }

                    // Imputado: no puede superar el restante. Si viene mal, lo ajustamos al restante.
                    $mAplicado = $m;
                    if ($mAplicado <= 0 || $mAplicado > $restante) {
                        $mAplicado = $restante;
                    }

                    if ($montoRecibido < $mAplicado) {
                        DB::rollBack();
                        return back()->with('error', "En EFECTIVO el monto recibido no puede ser menor al monto imputado.");
                    }

                    $vuelto = $montoRecibido - $mAplicado;

                    FormaCobroDetalle::create([
                        'id_cobro' => $id_cobro,
                        'items' => $i + 1,
                        'id_formacobro' => $formaId,
                        'identidademisora' => $banco ?: null,
                        'documento' => $doc ?: null,
                        'fecha' => $fecha,
                        'fecha_vencimiento' => $venc ?: null,
                        'monto_detformacobro' => $mAplicado,
                        'monto_recibido' => $montoRecibido,
                        'vuelto' => $vuelto,
                    ]);

                    $totalImputado += $mAplicado;
                    continue;
                }

                // NO efectivo: imputado debe ser > 0 y no superar el restante
                if ($m <= 0) {
                    DB::rollBack();
                    return back()->with('error', 'Monto inválido. Debe ser mayor a 0.');
                }
                if ($m > $restante) {
                    DB::rollBack();
                    return back()->with('error', 'El monto imputado no puede superar el saldo a cobrar.');
                }

                FormaCobroDetalle::create([
                    'id_cobro' => $id_cobro,
                    'items' => $i + 1,
                    'id_formacobro' => $formaId,
                    'identidademisora' => $banco ?: null,
                    'documento' => $doc ?: null,
                    'fecha' => $fecha,
                    'fecha_vencimiento' => $venc ?: null,
                    'monto_detformacobro' => $m,
                    'monto_recibido' => null,
                    'vuelto' => null,
                ]);

                $totalImputado += $m;
            }

            if ($totalImputado <= 0) {
                DB::rollBack();
                return back()->with('error', 'El total pagado debe ser mayor a 0.');
            }
            if ($totalImputado > $montoCobroOriginal) {
                DB::rollBack();
                return back()->with('error', 'El total pagado NO puede superar el total a cobrar.');
            }

            // CONTADO: no permitir parcial
            if (!$allowPartial && $totalImputado !== $montoCobroOriginal) {
                DB::rollBack();
                return back()->with('error', 'En CONTADO no se permiten pagos parciales. Debe completar el total.');
            }

            // CRÉDITO: permitir parcial. Si es parcial, ajustar det_cobro y cabecera al imputado (flujo 1 factura)
            if ($allowPartial && $totalImputado < $montoCobroOriginal) {
                $cantFacturas = (int) DB::table('det_cobro')->where('id_cobro', $id_cobro)->count();
                if ($cantFacturas !== 1) {
                    DB::rollBack();
                    return back()->with('error', 'Cobro parcial solo permitido cuando hay 1 factura en el cobro.');
                }

                $detalleCobro = CobroDetalle::where('id_cobro', $id_cobro)->firstOrFail();
                $detalleCobro->monto_detcobro = $totalImputado;
                $detalleCobro->save();

                $cobro->monto_cobro = $totalImputado;
                $cobro->save();
            }

            $cobro->cobro_estado = 'Realizado';
            $cobro->usuario = Auth::user()->name;
            $cobro->save();

            $this->recalcularCuentaCobrarPorCobro((int) $id_cobro);

            $hashService = app(LegalDocumentHashService::class);
            $cobro->forceFill([
                'hash_documento' => $hashService->hashCobro((int) $id_cobro),
                'hash_version' => LegalDocumentHashService::VERSION,
            ])->save();

            DB::commit();
            return redirect()->route('cobro.show', $id_cobro)->with('success', 'Cobro finalizado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al finalizar: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $idNotaCredito = $this->notaCreditoPorCobro((int) $id);
        if ($idNotaCredito !== null) {
            return redirect()
                ->route('nota_creditov.comprobante', $idNotaCredito)
                ->with('info', 'Este movimiento pertenece a una nota de credito. Se muestra su comprobante propio.');
        }

        $cobros = DB::table('cobros as c')
            ->join('apertura as a', 'c.idapertura', '=', 'a.idapertura')
            ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
            ->join('clientes as cli', 'c.idcliente', '=', 'cli.idcliente')
            ->join('cajas as ca', 'c.idcaja', '=', 'ca.idcaja')
            ->select(
                'c.id_cobro',
                'c.fecha_cobro',
                'c.monto_cobro',
                'c.cobro_estado',
                'c.usuario',
                'c.hash_documento',
                'c.hash_anulacion',
                'c.hash_version',
                's.descripcion as sucursal',
                'cli.nombre as cliente',
                'cli.num_documento',
                'ca.descripcion as caja',
                'c.idapertura'
            )
            ->where('c.id_cobro', $id)
            ->first();

        if (!$cobros) {
            return redirect()->route('cobro.index')->with('error', 'Cobro no encontrado.');
        }

        $cobrodetalle = DB::table('det_cobro as d')
            ->join('ventas as v', 'd.idventa', '=', 'v.idventa')
            ->select('d.items', 'd.monto_detcobro', 'v.idventa', 'v.nro_factura', 'v.condicion', 'v.totalventa')
            ->where('d.id_cobro', $id)
            ->orderBy('d.items')
            ->get();

        $formacobrodetalle = DB::table('det_formacobro as d')
            ->leftJoin('formacobro as fc', 'd.id_formacobro', '=', 'fc.id_formacobro')
            ->leftJoin('entidademisora as e', 'd.identidademisora', '=', 'e.identidademisora')
            ->select(
                'd.items',
                'fc.descripcion as formacobro',
                'e.descripcion as banco',
                'd.documento',
                'd.fecha',
                'd.fecha_vencimiento',
                'd.monto_detformacobro',
                'd.monto_recibido',
                'd.vuelto'
            )
            ->where('d.id_cobro', $id)
            ->orderBy('d.items')
            ->get();

        $totalPagado = (int) DB::table('det_formacobro')->where('id_cobro', $id)->sum('monto_detformacobro');
        $hashService = app(LegalDocumentHashService::class);
        $hashValido = $hashService->verificar($cobros->hash_documento ?? null, $hashService->hashCobro((int) $id));

        return view('ventas.cobro.show', compact('cobros', 'cobrodetalle', 'formacobrodetalle', 'totalPagado', 'hashValido'));
    }

    public function imprimirRecibo($id)
    {
        if ($this->cobroEsNotaCredito((int) $id)) {
            return redirect()
                ->route('cobro.index')
                ->with('error', 'Este movimiento corresponde a una nota de credito. No genera recibo de cobro; use el comprobante de nota de credito.');
        }

        $cobros = DB::table('cobros as c')
            ->join('apertura as a', 'c.idapertura', '=', 'a.idapertura')
            ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
            ->join('clientes as cli', 'c.idcliente', '=', 'cli.idcliente')
            ->join('cajas as ca', 'c.idcaja', '=', 'ca.idcaja')
            ->select(
                'c.id_cobro',
                'c.fecha_cobro',
                'c.monto_cobro',
                'c.cobro_estado',
                'c.usuario',
                'c.hash_documento',
                'c.hash_anulacion',
                'c.hash_version',
                's.descripcion as sucursal',
                'cli.nombre as cliente',
                'cli.num_documento',
                'ca.descripcion as caja',
                'c.idapertura'
            )
            ->where('c.id_cobro', $id)
            ->first();

        if (!$cobros) {
            return redirect()->route('cobro.index')->with('error', 'Cobro no encontrado.');
        }

        if ($cobros->cobro_estado !== 'Realizado') {
            return redirect()->route('cobro.show', $id)->with('error', 'Solo se puede imprimir recibo de cobros realizados.');
        }

        $cobrodetalle = DB::table('det_cobro as d')
            ->join('ventas as v', 'd.idventa', '=', 'v.idventa')
            ->leftJoin('cuenta_cobrar as cc', 'v.idventa', '=', 'cc.idventa')
            ->select(
                'd.items',
                'd.monto_detcobro',
                'v.idventa',
                'v.nro_factura',
                'v.condicion',
                'v.totalventa',
                'cc.importe',
                'cc.monto_pago',
                'cc.saldo',
                'cc.estado as estado_cuenta',
                'cc.fecha_vencimiento'
            )
            ->where('d.id_cobro', $id)
            ->orderBy('d.items')
            ->get();

        $formacobrodetalle = DB::table('det_formacobro as d')
            ->leftJoin('formacobro as fc', 'd.id_formacobro', '=', 'fc.id_formacobro')
            ->leftJoin('entidademisora as e', 'd.identidademisora', '=', 'e.identidademisora')
            ->select(
                'd.items',
                'fc.descripcion as formacobro',
                'e.descripcion as banco',
                'd.documento',
                'd.fecha',
                'd.fecha_vencimiento',
                'd.monto_detformacobro',
                'd.monto_recibido',
                'd.vuelto'
            )
            ->where('d.id_cobro', $id)
            ->orderBy('d.items')
            ->get();

        $totalPagado = (int) DB::table('det_formacobro')
            ->where('id_cobro', $id)
            ->sum('monto_detformacobro');
        $hashService = app(LegalDocumentHashService::class);
        $hashValido = $hashService->verificar($cobros->hash_documento ?? null, $hashService->hashCobro((int) $id));

        return view('ventas.cobro.recibo', compact(
            'cobros',
            'cobrodetalle',
            'formacobrodetalle',
            'totalPagado',
            'hashValido'
        ));
    }
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $cobro = Cobro::where('id_cobro', $id)
                ->lockForUpdate()
                ->first();

            if (!$cobro) {
                DB::rollBack();
                return redirect()->route('cobro.index')->with('error', 'Cobro no encontrado.');
            }

            if ($this->cobroEsNotaCredito((int) $id)) {
                DB::rollBack();
                return redirect()->route('cobro.index')->with('error', 'Este movimiento pertenece a una nota de credito. La anulacion debe hacerse desde Nota de Credito.');
            }

            if ($cobro->cobro_estado === 'Anulado') {
                DB::rollBack();
                return redirect()->route('cobro.index')->with('error', 'El cobro ya se encuentra anulado.');
            }

            $cobro->cobro_estado = 'Anulado';
            $cobro->usuario = Auth::user()->name;
            $cobro->hash_anulacion = app(LegalDocumentHashService::class)
                ->hashAnulacion('COBRO', (int) $cobro->id_cobro, request('motivo_anulacion'), Auth::user()->name ?? null);
            $cobro->save();

            $this->recalcularCuentaCobrarPorCobro((int) $id);

            DB::commit();

            return redirect()->route('cobro.index')->with('info', 'Cobro anulado correctamente. Se generó nuevamente el saldo pendiente para cobrar.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('cobro.index')->with('error', 'Error al anular: ' . $e->getMessage());
        }
    }

    public function deleteFactura($id_cobro, $id_detcobro)
    {
        DB::beginTransaction();
        try {

            $cobro = Cobro::find($id_cobro);

            if (!$cobro || $cobro->cobro_estado !== 'Pendiente') {
                DB::rollBack();
                return redirect()->route('cobro.index')
                    ->with('error', 'Solo se pueden modificar cobros en estado Pendiente.');
            }

            DB::table('det_cobro')
                ->where('id_cobro', $id_cobro)
                ->where('id_detcobro', $id_detcobro)
                ->delete();

            $nuevoTotalCobro = (int) DB::table('det_cobro')
                ->where('id_cobro', $id_cobro)
                ->sum('monto_detcobro');

            $cobro->monto_cobro = $nuevoTotalCobro;
            $cobro->save();

            $this->recalcularCuentaCobrarPorCobro((int) $id_cobro);

            DB::commit();
            return redirect()->route('cobro.edit', $id_cobro)->with('info', 'Factura eliminada del cobro.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('cobro.edit', $id_cobro)->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deletePago($id_cobro, $id_detformacobro)
    {
        DB::beginTransaction();
        try {
            $cobro = Cobro::find($id_cobro);

            if (!$cobro || $cobro->cobro_estado !== 'Pendiente') {
                DB::rollBack();
                return redirect()->route('cobro.index')
                    ->with('error', 'Solo se pueden modificar cobros en estado Pendiente.');
            }

            DB::table('det_formacobro')
                ->where('id_cobro', $id_cobro)
                ->where('id_detformacobro', $id_detformacobro)
                ->delete();

            DB::commit();
            return redirect()->route('cobro.edit', $id_cobro)->with('info', 'Pago eliminado.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('cobro.edit', $id_cobro)->with('error', 'Error: ' . $e->getMessage());
        }
    }

    private function recalcularCuentaCobrarPorCobro(int $id_cobro): void
    {
        $cobroReferencia = DB::table('cobros')
            ->where('id_cobro', $id_cobro)
            ->lockForUpdate()
            ->first();

        if (!$cobroReferencia) {
            throw new \Exception("No existe el cobro {$id_cobro}.");
        }
        $ventas = DB::table('det_cobro')
            ->where('id_cobro', $id_cobro)
            ->select('idventa')
            ->distinct()
            ->pluck('idventa');

        foreach ($ventas as $idventa) {
            $venta = DB::table('ventas')
                ->where('idventa', $idventa)
                ->lockForUpdate()
                ->first();

            if (!$venta) {
                throw new \Exception("No existe la venta {$idventa}.");
            }

            $pagadoReal = (int) DB::table('det_cobro as dc')
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

            $cta = DB::table('cuenta_cobrar')
                ->where('idventa', $idventa)
                ->lockForUpdate()
                ->first();

            if (!$cta) {
                throw new \Exception("No existe cuenta a cobrar para la venta {$idventa}.");
            }

            $importe = $this->importeVentaAjustado((int) $idventa, (int) $venta->totalventa);
            //Evita que por error el monto pagado supere el importe.
            $pagadoReal = min($pagadoReal, $importe);
            $saldo = max(0, $importe - $pagadoReal);

            $estado = ($saldo == 0) ? 'Pagado' : (($pagadoReal > 0) ? 'Parcial' : 'Generado');

            DB::table('cuenta_cobrar')
                ->where('idcuenta_cobrar', $cta->idcuenta_cobrar)
                ->update([
                    'importe'    => $importe,
                    'monto_pago' => $pagadoReal,
                    'saldo' => $saldo,
                    'estado' => $estado,
                ]);

            $ventaAuditada = Ventas::findOrFail($idventa);
            $ventaAuditada->saldo_factura = $saldo;
            $ventaAuditada->estado = ($saldo == 0 ? 'Realizado' : 'Pendiente');
            $ventaAuditada->save();
            $this->sincronizarCobroPendientePorVenta((int) $idventa, $cobroReferencia);
        }
    }

    private function importeVentaAjustado(int $idventa, int $totalVenta): int
    {
        $totalCredito = 0;
        $totalDebito = 0;

        if (Schema::hasTable('nota_credito_venta')) {
            $totalCredito = (int) DB::table('nota_credito_venta')
                ->where('idventa', $idventa)
                ->whereNotIn('estado', ['A', 'Anulado', 'Anulada', 'Cancelado', 'Cancelada'])
                ->sum('totalventa');
        }

        if (Schema::hasTable('nota_debito_venta')) {
            $totalDebito = (int) DB::table('nota_debito_venta')
                ->where('idventa', $idventa)
                ->whereNotIn('estado', ['A', 'Anulado', 'Anulada', 'Cancelado', 'Cancelada'])
                ->sum('totalventa');
        }

        return max(0, $totalVenta + $totalDebito - $totalCredito);
    }

    private function sincronizarCobroPendientePorVenta(int $idventa, object $cobroReferencia): void
    {
        $cta = DB::table('cuenta_cobrar')
            ->where('idventa', $idventa)
            ->lockForUpdate()
            ->first();

        if (!$cta) {
            throw new \Exception("No existe cuenta a cobrar para la venta {$idventa}.");
        }

        $venta = DB::table('ventas')
            ->where('idventa', $idventa)
            ->first();

        if (!$venta) {
            throw new \Exception("No existe la venta {$idventa}.");
        }

        if (in_array($venta->estado, ['A', 'Anulado', 'Cancelado'], true)) {
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
                $cobroPendiente->usuario = Auth::user()->name;
                $cobroPendiente->save();
            }

            return;
        }

        if ($pendiente) {
            $cobroPendiente = Cobro::findOrFail($pendiente->id_cobro);
            $cobroPendiente->monto_cobro = $saldo;
            $cobroPendiente->usuario = Auth::user()->name;
            $cobroPendiente->save();

            $detallePendiente = CobroDetalle::findOrFail($pendiente->id_detcobro);
            $detallePendiente->monto_detcobro = $saldo;
            $detallePendiente->save();

            return;
        }

        $nuevoCobro = Cobro::create([
            'idcaja'       => $cobroReferencia->idcaja,
            'idsucursal'   => $cobroReferencia->idsucursal,
            'idapertura'   => $cobroReferencia->idapertura,
            'fecha_cobro'  => now()->toDateString(),
            'idcliente'    => $venta->idcliente,
            'monto_cobro'  => $saldo,
            'cobro_estado' => 'Pendiente',
            'usuario'      => Auth::user()->name,
        ]);

        CobroDetalle::create([
            'id_cobro'       => $nuevoCobro->id_cobro,
            'items'          => 1,
            'idventa'        => $idventa,
            'monto_detcobro' => $saldo,
        ]);
    }

    private function cobroEsNotaCredito(int $idCobro): bool
    {
        if (DB::table('nota_credito_venta_cobro')
            ->where('id_cobro', $idCobro)
            ->exists()) {
            return true;
        }

        return DB::table('det_formacobro')
            ->where('id_cobro', $idCobro)
            ->where(function ($q) {
                $q->where('documento', 'LIKE', 'NC %')
                    ->orWhere('documento', 'LIKE', 'AJUSTE NC %')
                    ->orWhere('documento', 'LIKE', 'REING NC %')
                    ->orWhere('documento', 'LIKE', 'REING AJUSTE NC %')
                    ->orWhere('documento', 'LIKE', 'REINGRESO AJUSTE NC %');
            })
            ->exists();
    }

    private function notaCreditoPorCobro(int $idCobro): ?int
    {
        $idNotaCredito = DB::table('nota_credito_venta_cobro')
            ->where('id_cobro', $idCobro)
            ->value('idnota_creditov');

        if ($idNotaCredito !== null) {
            return (int) $idNotaCredito;
        }

        $documento = DB::table('det_formacobro')
            ->where('id_cobro', $idCobro)
            ->where(function ($q) {
                $q->where('documento', 'LIKE', 'NC %')
                    ->orWhere('documento', 'LIKE', 'AJUSTE NC %')
                    ->orWhere('documento', 'LIKE', 'REING NC %')
                    ->orWhere('documento', 'LIKE', 'REING AJUSTE NC %')
                    ->orWhere('documento', 'LIKE', 'REINGRESO AJUSTE NC %');
            })
            ->value('documento');

        if (is_string($documento) && preg_match('/NC\s+(\d+)/i', $documento, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function ventaCreditoSinAceptar(int $idventa): bool
    {
        $venta = DB::table('ventas')
            ->where('idventa', $idventa)
            ->select('condicion')
            ->first();

        if (! $venta || mb_strtolower(trim((string) $venta->condicion)) === 'contado') {
            return false;
        }

        return ! DB::table('venta_credito_aceptaciones')
            ->where('idventa', $idventa)
            ->where('estado', 'Aceptado')
            ->exists();
    }

    private function cobroTieneCreditoSinAceptar(int $idCobro): bool
    {
        return DB::table('det_cobro')
            ->where('id_cobro', $idCobro)
            ->pluck('idventa')
            ->contains(fn ($idventa) => $this->ventaCreditoSinAceptar((int) $idventa));
    }
}
