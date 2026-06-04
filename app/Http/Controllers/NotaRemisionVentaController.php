<?php

namespace App\Http\Controllers;

use App\Models\NotaRemisionVenta;
use App\Models\NotaRemisionVentaDetalle;
use App\Services\LegalDocumentHashService;
use App\Services\MovimientoStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotaRemisionVentaController extends Controller
{
    private const ESTADOS_ANULADOS = ['A', 'ANULADO', 'ANULADA', 'CANCELADO', 'CANCELADA'];

    private const MOTIVOS_TRASLADO = [
        'Venta',
        'Consignacion',
        'Exportacion',
        'Compra',
        'Importacion',
        'Devolucion',
        'Traslado entre locales de la misma empresa',
        'Traslado de bienes para transformacion',
        'Traslado de bienes para reparacion',
        'Traslado por emisor movil',
        'Exhibicion o demostracion',
        'Participacion en ferias',
        'Otros',
    ];

    private const MOTIVOS_REQUIEREN_VENTA = ['Venta'];
    private const MOTIVOS_REQUIEREN_DEPOSITO_DESTINO = ['Traslado entre locales de la misma empresa'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = $request->get('searchText');
        $query2 = $request->get('searchText2');
        $query3 = $request->get('searchText3');
        $query4 = $request->get('searchText4');
        $query5 = $request->get('searchText5');
        $query6 = $request->get('searchText6');

        $nota_remision = DB::table('nota_remision_venta as nr')
            ->leftJoin('ventas as v', 'nr.idventa', '=', 'v.idventa')
            ->leftJoin('clientes as cli', 'nr.idcliente', '=', 'cli.idcliente')
            ->leftJoin('destinatarios_remision as dr', 'nr.iddestinatario_remision', '=', 'dr.iddestinatario_remision')
            ->leftJoin('depositos as dor', 'nr.iddeposito_origen', '=', 'dor.iddeposito')
            ->leftJoin('sucursales as so', 'dor.idsucursal', '=', 'so.idsucursal')
            ->leftJoin('sucursales as sv', 'v.idsucursal', '=', 'sv.idsucursal')
            ->leftJoin('chofer as ch', 'nr.idchofer', '=', 'ch.idchofer')
            ->leftJoin('vehiculo as vh', 'nr.idvehiculo', '=', 'vh.idvehiculo')
            ->leftJoin('timbrado as trm', 'nr.idtimbrado', '=', 'trm.idtimbrado')
            ->leftJoin('users as u', 'nr.idusuario', '=', 'u.id')
            ->select(
                'nr.idnota_remision_venta',
                'nr.nro_remision',
                'nr.idventa',
                'nr.tipo_origen',
                'nr.fecha_emision',
                'nr.fecha_inicio_traslado',
                'nr.fecha_fin_traslado',
                'nr.motivo_traslado',
                'nr.recibido_por',
                'nr.documento_receptor',
                'nr.fecha_entrega',
                'nr.recepcion_registrada_at',
                'nr.estado',
                'u.name as usuario',
                'trm.nro_timbrado as timbrado',
                DB::raw("COALESCE(v.nro_factura, '-') as nro_factura"),
                DB::raw("COALESCE(v.condicion, '-') as condicion"),
                DB::raw("COALESCE(cli.nombre, dr.nombre, '-') as cliente"),
                DB::raw("COALESCE(cli.num_documento, dr.documento, '-') as num_documento"),
                DB::raw("COALESCE(so.descripcion, '-') as sucursal"),
                DB::raw("CONCAT(COALESCE(ch.nombre, ''), ' ', COALESCE(ch.apellido, '')) as chofer"),
                DB::raw("COALESCE(vh.nrochapa, '-') as vehiculo")
            )
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('nr.idnota_remision_venta', 'LIKE', "%{$query}%")
                        ->orWhere('nr.nro_remision', 'LIKE', "%{$query}%");
                });
            })
            ->when($query2, function ($q) use ($query2) {
                $q->whereRaw("COALESCE(cli.nombre, dr.nombre, '') LIKE ?", ["%{$query2}%"]);
            })
            ->when($query3, fn ($q) => $q->whereDate('nr.fecha_emision', $query3))
            ->when($query4, function ($q) use ($query4) {
                $q->whereRaw("COALESCE(sv.descripcion, so.descripcion, '') LIKE ?", ["%{$query4}%"]);
            })
            ->when($query5, function ($q) use ($query5) {
                $q->where('v.nro_factura', 'LIKE', "%{$query5}%");
            })
            ->when($query6, function ($q) use ($query6) {
                $q->whereRaw("COALESCE(cli.num_documento, dr.documento, '') LIKE ?", ["%{$query6}%"]);
            })
            ->orderByDesc('nr.idnota_remision_venta')
            ->paginate(10);

        return view('ventas.nota_remision.index', compact('nota_remision', 'query', 'query2', 'query3', 'query4', 'query5', 'query6'));
    }

    public function create(Request $request, ?int $idventa = null)
    {
        $idventa = $idventa ?: ((int) $request->get('idventa') ?: null);
        $venta = null;

        if ($idventa) {
            $venta = $this->ventaValida($idventa);
            $remision = $this->remisionVigente($idventa);

            if ($remision) {
                return redirect()->route('nota_remision_venta.show', $remision->idnota_remision_venta)
                    ->with('info', 'Esta venta ya tiene nota de remision vigente.');
            }
        }

        $idsucursalPreview = $venta
            ? (int) $venta->idsucursal
            : $this->idsucursalDesdeDeposito((int) old('iddeposito_origen', 0));

        if ($idsucursalPreview <= 0) {
            $idsucursalPreview = (int) Auth::user()->trabaja_sucursal;
        }

        $timbradoPreview = $this->timbradoActivo($idsucursalPreview);
        $previewTimbrado = $timbradoPreview->nro_timbrado ?? '';
        $previewNroRemision = $timbradoPreview
            ? $this->generarNumeroRemision($this->proximoIdTabla('nota_remision_venta', 'idnota_remision_venta'), $timbradoPreview->nro_serie)
            : 'Sin timbrado activo';

        return view('ventas.nota_remision.create', [
            'venta' => $venta,
            'ventas' => $this->ventasElegibles(),
            'choferes' => DB::table('chofer')->where('estado', 'Activo')->orderBy('nombre')->get(),
            'vehiculos' => DB::table('vehiculo')->where('estado', 'Activo')->orderBy('nrochapa')->get(),
            'transportistas' => DB::table('transportistas')->where('estado', 'Activo')->orderBy('nombre')->get(),
            'destinatarios' => DB::table('destinatarios_remision')->where('estado', 'Activo')->orderBy('nombre')->get(),
            'motivosTraslado' => self::MOTIVOS_TRASLADO,
            'productos' => $this->productosActivos(),
            'depositos' => $this->depositosActivos(),
            'previewNroRemision' => $previewNroRemision,
            'previewTimbrado' => $previewTimbrado,
        ]);
    }

    public function store(Request $request, ?int $idventa = null)
    {
        $idventa = $idventa ?: ((int) $request->input('idventa') ?: null);
        $motivo = (string) $request->input('motivo_traslado');

        if (in_array($motivo, self::MOTIVOS_REQUIEREN_VENTA, true) && ! $idventa) {
            return back()->withInput()->with('error', 'El motivo Venta requiere seleccionar una factura/venta asociada.');
        }

        $request->merge(['idventa' => $idventa]);
        $requiereDestinoDeposito = in_array($motivo, self::MOTIVOS_REQUIEREN_DEPOSITO_DESTINO, true);

        $request->validate([
            'idventa' => ['nullable', 'integer', 'exists:ventas,idventa'],
            'idchofer' => ['required', 'integer', 'exists:chofer,idchofer'],
            'idvehiculo' => ['required', 'integer', 'exists:vehiculo,idvehiculo'],
            'idtransportista' => ['required', 'integer', 'exists:transportistas,idtransportista'],
            'iddestinatario_remision' => [$idventa || $requiereDestinoDeposito ? 'nullable' : 'required', 'integer', 'exists:destinatarios_remision,iddestinatario_remision'],
            'fecha_emision' => ['required', 'date'],
            'fecha_inicio_traslado' => ['required', 'date'],
            'fecha_fin_traslado' => ['required', 'date', 'after_or_equal:fecha_inicio_traslado'],
            'motivo_traslado' => ['required', 'string', 'max:80'],
            'punto_partida' => ['required', 'string', 'max:180'],
            'punto_llegada' => ['required', 'string', 'max:180'],
            'ciudad_partida' => ['nullable', 'string', 'max:100'],
            'departamento_partida' => ['nullable', 'string', 'max:100'],
            'ciudad_llegada' => ['nullable', 'string', 'max:100'],
            'departamento_llegada' => ['nullable', 'string', 'max:100'],
            'recibido_por' => ['nullable', 'string', 'max:150'],
            'documento_receptor' => ['nullable', 'string', 'max:50'],
            'observacion' => ['nullable', 'string', 'max:255'],
            'iddeposito_origen' => [$idventa ? 'nullable' : 'required', 'integer', 'exists:depositos,iddeposito'],
            'iddeposito_destino' => [$requiereDestinoDeposito ? 'required' : 'nullable', 'integer', 'exists:depositos,iddeposito'],
            'idproducto' => [$idventa ? 'nullable' : 'required', 'array', 'min:1'],
            'idproducto.*' => ['required_with:idproducto', 'integer', 'exists:productos,idproducto'],
            'cantidad' => [$idventa ? 'nullable' : 'required', 'array', 'min:1'],
            'cantidad.*' => ['required_with:cantidad', 'numeric', 'min:0.001'],
        ], [
            'idchofer.required' => 'Debe seleccionar el chofer para respaldar el traslado.',
            'idvehiculo.required' => 'Debe seleccionar el vehiculo para respaldar el traslado.',
            'idtransportista.required' => 'Debe seleccionar el transportista para respaldar el traslado.',
            'iddestinatario_remision.required' => 'Debe seleccionar el destinatario cuando la remision no tiene venta asociada.',
            'fecha_inicio_traslado.required' => 'Debe indicar la fecha de inicio del traslado.',
            'fecha_fin_traslado.after_or_equal' => 'La fecha final del traslado no puede ser menor a la fecha inicial.',
            'punto_partida.required' => 'Debe indicar el punto de partida.',
            'punto_llegada.required' => 'Debe indicar el punto de llegada.',
            'iddeposito_origen.required' => 'Debe indicar el deposito de origen cuando la remision no tiene venta asociada.',
            'iddeposito_destino.required' => 'Para traslado entre locales debe indicar el deposito de destino.',
            'idproducto.required' => 'Debe cargar al menos un producto cuando la remision no tiene venta asociada.',
            'cantidad.required' => 'Debe cargar la cantidad de los productos.',
        ]);

        if (
            $requiereDestinoDeposito
            && ! $idventa
            && (int) $request->input('iddeposito_origen') === (int) $request->input('iddeposito_destino')
        ) {
            return back()->withInput()->with('error', 'El deposito de origen y destino no pueden ser el mismo para un traslado interno.');
        }

        $venta = $idventa ? $this->ventaValida($idventa) : null;

        if ($idventa && $this->remisionVigente($idventa)) {
            return redirect()->route('nota_remision_venta.index')->with('info', 'Esta venta ya tiene nota de remision vigente.');
        }

        $iddepositoOrigen = $venta ? (int) $venta->iddeposito : (int) $request->input('iddeposito_origen');
        $depositoOrigen = DB::table('depositos')->where('iddeposito', $iddepositoOrigen)->first();
        $idsucursalBase = $venta ? (int) $venta->idsucursal : (int) ($depositoOrigen->idsucursal ?? 0);
        $timbrado = $this->timbradoActivo($idsucursalBase);

        if (! $timbrado) {
            return back()->withInput()->with('error', 'No existe timbrado activo para la sucursal de origen.');
        }

        try {
            return DB::transaction(function () use ($request, $venta, $idventa, $timbrado, $iddepositoOrigen) {
                $remision = NotaRemisionVenta::create([
                    'idventa' => $idventa,
                    'idcliente' => $venta ? (int) $venta->idcliente : null,
                    'iddestinatario_remision' => $venta ? null : ($request->input('iddestinatario_remision') ?: null),
                    'tipo_origen' => $venta ? 'Venta' : 'Libre',
                    'idchofer' => (int) $request->input('idchofer'),
                    'idvehiculo' => (int) $request->input('idvehiculo'),
                    'idtransportista' => (int) $request->input('idtransportista'),
                    'idtimbrado' => (int) $timbrado->idtimbrado,
                    'iddeposito_origen' => $iddepositoOrigen,
                    'iddeposito_destino' => $request->input('iddeposito_destino') ?: null,
                    'fecha_emision' => $request->input('fecha_emision'),
                    'fecha_traslado' => $request->input('fecha_inicio_traslado'),
                    'fecha_inicio_traslado' => $request->input('fecha_inicio_traslado'),
                    'fecha_fin_traslado' => $request->input('fecha_fin_traslado'),
                    'motivo_traslado' => $request->input('motivo_traslado'),
                    'punto_partida' => $request->input('punto_partida'),
                    'punto_llegada' => $request->input('punto_llegada'),
                    'ciudad_partida' => $request->input('ciudad_partida'),
                    'departamento_partida' => $request->input('departamento_partida'),
                    'ciudad_llegada' => $request->input('ciudad_llegada'),
                    'departamento_llegada' => $request->input('departamento_llegada'),
                    'recibido_por' => $request->input('recibido_por'),
                    'documento_receptor' => $request->input('documento_receptor'),
                    'estado' => 'Emitido',
                    'idusuario' => Auth::id(),
                    'observacion' => $request->input('observacion'),
                ]);

                $detalles = $venta
                    ? $this->detallesDesdeVenta((int) $idventa)
                    : $this->detallesDesdeFormulario($request);

                if ($detalles->isEmpty()) {
                    throw new \RuntimeException('Debe cargar al menos un detalle para remitir.');
                }

                foreach ($detalles as $detalle) {
                    NotaRemisionVentaDetalle::create([
                        'idnota_remision_venta' => $remision->idnota_remision_venta,
                        'idproducto' => $detalle->idproducto,
                        'items' => $detalle->items,
                        'cantidad' => $detalle->cantidad,
                    ]);
                }

                $this->aplicarStockTraslado($remision, $detalles);

                $remision->forceFill([
                    'nro_remision' => $this->generarNumeroRemision($remision->idnota_remision_venta, $timbrado->nro_serie),
                ]);

                $remision->forceFill([
                    'hash_documento' => app(LegalDocumentHashService::class)->hashNotaRemision((int) $remision->idnota_remision_venta),
                    'hash_version' => LegalDocumentHashService::VERSION,
                ])->save();

                return redirect()
                    ->route('nota_remision_venta.show', $remision->idnota_remision_venta)
                    ->with('success', 'Nota de remision generada correctamente.');
            });
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Error al registrar nota de remision: ' . $e->getMessage());
        }
    }

    public function show(int $idremision)
    {
        $datos = $this->datosRemision($idremision);
        $hashService = app(LegalDocumentHashService::class);
        $datos['hashValido'] = $hashService->verificar(
            $datos['remision']->hash_documento ?? null,
            $hashService->hashNotaRemision($idremision)
        );

        return view('ventas.nota_remision.show', $datos);
    }

    public function showForVenta(int $idventa, int $idremision)
    {
        return redirect()->route('nota_remision_venta.show', $idremision);
    }

    public function edit(int $idremision)
    {
        $datos = $this->datosRemision($idremision);

        if ($this->estaAnulada((string) $datos['remision']->estado)) {
            return redirect()->route('nota_remision_venta.show', $idremision)
                ->with('error', 'No se puede completar recepcion de una nota de remision anulada.');
        }

        return view('ventas.nota_remision.edit', $datos);
    }

    public function update(Request $request, int $idremision)
    {
        $request->validate([
            'recibido_por' => ['required', 'string', 'max:150'],
            'documento_receptor' => ['required', 'string', 'max:50'],
            'fecha_entrega' => ['required', 'date'],
            'hora_entrega' => ['nullable', 'date_format:H:i'],
            'observacion_entrega' => ['nullable', 'string', 'max:255'],
        ], [
            'recibido_por.required' => 'Debe indicar quien recibio la mercaderia.',
            'documento_receptor.required' => 'Debe indicar el documento de quien recibio.',
            'fecha_entrega.required' => 'Debe indicar la fecha de entrega.',
            'hora_entrega.date_format' => 'La hora de entrega debe tener formato HH:MM.',
        ]);

        $remision = NotaRemisionVenta::findOrFail($idremision);

        if ($this->estaAnulada((string) $remision->estado)) {
            return redirect()->route('nota_remision_venta.show', $idremision)
                ->with('error', 'No se puede completar recepcion de una nota de remision anulada.');
        }

        $horaEntrega = $request->input('hora_entrega') ?: now()->format('H:i');

        $remision->forceFill([
            'recibido_por' => $request->input('recibido_por'),
            'documento_receptor' => $request->input('documento_receptor'),
            'fecha_entrega' => $request->input('fecha_entrega'),
            'hora_entrega' => $horaEntrega,
            'observacion_entrega' => $request->input('observacion_entrega'),
            'idusuario_recepcion' => Auth::id(),
            'recepcion_registrada_at' => now(),
            'estado' => 'Entregado',
        ]);

        $remision->forceFill([
            'hash_recepcion' => app(LegalDocumentHashService::class)->hashNotaRemision((int) $remision->idnota_remision_venta, true),
            'hash_version' => LegalDocumentHashService::VERSION,
        ])->save();

        return redirect()->route('nota_remision_venta.show', $idremision)
            ->with('success', 'Recepcion de la nota de remision registrada correctamente.');
    }

    public function comprobante(int $idremision)
    {
        $datos = $this->datosRemision($idremision);
        $hashService = app(LegalDocumentHashService::class);
        $datos['hashValido'] = $hashService->verificar(
            $datos['remision']->hash_documento ?? null,
            $hashService->hashNotaRemision($idremision)
        );

        return view('ventas.nota_remision.comprobante', $datos);
    }

    public function comprobanteForVenta(int $idventa, int $idremision)
    {
        return redirect()->route('nota_remision_venta.comprobante', $idremision);
    }

    public function destroy(Request $request, int $idremision)
    {
        $request->validate([
            'motivo_anulacion' => ['nullable', 'string', 'max:255'],
        ]);

        $remision = NotaRemisionVenta::findOrFail($idremision);

        if ($this->estaAnulada((string) $remision->estado)) {
            return redirect()->route('nota_remision_venta.index')->with('info', 'La nota de remision ya esta anulada.');
        }

        try {
            DB::transaction(function () use ($remision, $request): void {
                $this->revertirStockTraslado($remision);

                $remision->forceFill([
                    'estado' => 'Anulado',
                    'motivo_anulacion' => $request->input('motivo_anulacion'),
                    'fecha_anulacion' => now(),
                    'hash_anulacion' => app(LegalDocumentHashService::class)
                        ->hashAnulacion('NOTA_REMISION', (int) $remision->idnota_remision_venta, $request->input('motivo_anulacion'), Auth::user()->name ?? null),
                ])->save();
            });
        } catch (\Throwable $e) {
            return redirect()->route('nota_remision_venta.index')
                ->with('error', 'No se pudo anular la nota de remision: ' . $e->getMessage());
        }

        return redirect()->route('nota_remision_venta.index')->with('success', 'Nota de remision anulada correctamente.');
    }

    private function datosRemision(int $idremision): array
    {
        $remision = DB::table('nota_remision_venta as nr')
            ->leftJoin('chofer as ch', 'nr.idchofer', '=', 'ch.idchofer')
            ->leftJoin('vehiculo as vh', 'nr.idvehiculo', '=', 'vh.idvehiculo')
            ->leftJoin('transportistas as tr', 'nr.idtransportista', '=', 'tr.idtransportista')
            ->leftJoin('destinatarios_remision as dr', 'nr.iddestinatario_remision', '=', 'dr.iddestinatario_remision')
            ->leftJoin('timbrado as trm', 'nr.idtimbrado', '=', 'trm.idtimbrado')
            ->leftJoin('depositos as do', 'nr.iddeposito_origen', '=', 'do.iddeposito')
            ->leftJoin('depositos as dd', 'nr.iddeposito_destino', '=', 'dd.iddeposito')
            ->leftJoin('sucursales as so', 'do.idsucursal', '=', 'so.idsucursal')
            ->leftJoin('sucursales as sd', 'dd.idsucursal', '=', 'sd.idsucursal')
            ->leftJoin('users as u', 'nr.idusuario', '=', 'u.id')
            ->leftJoin('users as ur', 'nr.idusuario_recepcion', '=', 'ur.id')
            ->select(
                'nr.*',
                'u.name as usuario',
                'ur.name as usuario_recepcion',
                'so.descripcion as sucursal_origen',
                'sd.descripcion as sucursal_destino',
                'do.descripcion as deposito_origen',
                'dd.descripcion as deposito_destino',
                'trm.nro_timbrado as nro_timbrado_remision',
                'dr.nombre as destinatario_nombre',
                'dr.documento as destinatario_documento',
                'dr.direccion as destinatario_direccion',
                'tr.nombre as transportista_nombre',
                'tr.documento as transportista_documento',
                'tr.direccion as transportista_direccion',
                DB::raw("CONCAT(COALESCE(ch.nombre, ''), ' ', COALESCE(ch.apellido, '')) as chofer"),
                'ch.ci as chofer_ci',
                'ch.direccion as chofer_direccion_final',
                'vh.nrochapa',
                'vh.modelo',
                'vh.chasis'
            )
            ->where('nr.idnota_remision_venta', $idremision)
            ->first();

        if (! $remision) {
            abort(404, 'Nota de remision no encontrada.');
        }

        $venta = $remision->idventa ? $this->ventaValida((int) $remision->idventa, false) : null;
        $detalles = DB::table('nota_remision_venta_detalle as nrd')
            ->join('productos as p', 'nrd.idproducto', '=', 'p.idproducto')
            ->where('nrd.idnota_remision_venta', $idremision)
            ->select(
                'nrd.idnota_remision_venta_detalle',
                'nrd.idnota_remision_venta',
                'nrd.idproducto',
                'nrd.items',
                'nrd.cantidad',
                DB::raw("CONCAT(COALESCE(p.codigo, ''), ' ', p.descripcion) as descripcion")
            )
            ->orderBy('nrd.items')
            ->get();

        return compact('venta', 'remision', 'detalles');
    }

    private function ventaValida(int $idventa, bool $bloquearAnulada = true): object
    {
        $venta = DB::table('ventas as v')
            ->join('clientes as c', 'v.idcliente', '=', 'c.idcliente')
            ->leftJoin('sucursales as s', 'v.idsucursal', '=', 's.idsucursal')
            ->leftJoin('timbrado as t', 'v.idtimbrado', '=', 't.idtimbrado')
            ->select(
                'v.*',
                'c.nombre as cliente',
                'c.num_documento as cliente_documento',
                'c.direccion as cliente_direccion',
                's.descripcion as sucursal',
                't.nro_timbrado'
            )
            ->where('v.idventa', $idventa)
            ->first();

        if (! $venta) {
            abort(404, 'Venta no encontrada.');
        }

        if ($bloquearAnulada && $this->estaAnulada((string) $venta->estado)) {
            abort(422, 'No se puede generar remision para una venta anulada.');
        }

        return $venta;
    }

    private function ventasElegibles()
    {
        $sucursal = Auth::user()->trabaja_sucursal;

        return DB::table('ventas as v')
            ->join('clientes as cli', 'v.idcliente', '=', 'cli.idcliente')
            ->where('v.idsucursal', $sucursal)
            ->whereNotIn('v.estado', self::ESTADOS_ANULADOS)
            ->whereNotExists(function ($sub) {
                $sub->selectRaw('1')
                    ->from('nota_remision_venta as nr')
                    ->whereColumn('nr.idventa', 'v.idventa')
                    ->whereNotIn('nr.estado', ['Anulado', 'Anulada', 'A']);
            })
            ->select('v.idventa', 'v.nro_factura', 'v.fecha', 'v.condicion', 'v.estado', 'cli.nombre as cliente', 'cli.num_documento')
            ->orderByDesc('v.idventa')
            ->get();
    }

    private function detallesDesdeVenta(int $idventa)
    {
        return DB::table('venta_detalle as vd')
            ->join('productos as p', 'vd.idproducto', '=', 'p.idproducto')
            ->where('vd.idventa', $idventa)
            ->select('vd.idproducto', 'vd.items', 'vd.cantidad', DB::raw("CONCAT(COALESCE(p.codigo, ''), ' ', p.descripcion) as descripcion"))
            ->orderBy('vd.items')
            ->get();
    }

    private function detallesDesdeFormulario(Request $request)
    {
        $productos = DB::table('productos')
            ->whereIn('idproducto', $request->input('idproducto', []))
            ->select('idproducto', 'codigo', 'descripcion')
            ->get()
            ->keyBy('idproducto');

        return collect($request->input('idproducto', []))
            ->values()
            ->map(function ($idproducto, $index) use ($request, $productos) {
                $producto = $productos[(int) $idproducto] ?? null;
                $cantidad = (float) ($request->input('cantidad')[$index] ?? 0);

                return (object) [
                    'idproducto' => (int) $idproducto,
                    'items' => $index + 1,
                    'cantidad' => $cantidad,
                    'descripcion' => $producto
                        ? trim(($producto->codigo ? $producto->codigo . ' ' : '') . $producto->descripcion)
                        : 'Producto ' . $idproducto,
                ];
            })
            ->filter(fn ($detalle) => $detalle->idproducto > 0 && $detalle->cantidad > 0)
            ->values();
    }

    private function remisionVigente(?int $idventa): ?object
    {
        if (! $idventa) {
            return null;
        }

        return DB::table('nota_remision_venta')
            ->where('idventa', $idventa)
            ->whereNotIn('estado', ['Anulado', 'Anulada', 'A'])
            ->orderByDesc('idnota_remision_venta')
            ->first();
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

    private function productosActivos()
    {
        return DB::table('productos')
            ->where('estado', 'Activo')
            ->select('idproducto', 'codigo', 'descripcion')
            ->orderBy('descripcion')
            ->get();
    }

    private function sucursalesActivas()
    {
        return DB::table('sucursales')
            ->where('estado', 'Activo')
            ->select('idsucursal', 'descripcion')
            ->orderBy('descripcion')
            ->get();
    }

    private function depositosActivos()
    {
        return DB::table('depositos as d')
            ->join('sucursales as s', 'd.idsucursal', '=', 's.idsucursal')
            ->where('d.estado', 'Activo')
            ->select('d.iddeposito', 'd.idsucursal', 'd.descripcion', 's.descripcion as sucursal')
            ->orderBy('s.descripcion')
            ->orderBy('d.descripcion')
            ->get();
    }

    private function aplicarStockTraslado(NotaRemisionVenta $remision, $detalles): void
    {
        if (! $this->requiereMovimientoStock($remision)) {
            return;
        }

        $depositoOrigen = DB::table('depositos')->where('iddeposito', $remision->iddeposito_origen)->first();
        $depositoDestino = DB::table('depositos')->where('iddeposito', $remision->iddeposito_destino)->first();

        if (! $depositoOrigen || ! $depositoDestino) {
            throw new \RuntimeException('No se pudo identificar el deposito de origen o destino para mover stock.');
        }

        foreach ($detalles as $detalle) {
            $cantidad = (float) $detalle->cantidad;
            $producto = DB::table('productos')->where('idproducto', $detalle->idproducto)->first();
            $stockOrigen = DB::table('stock')
                ->where('iddeposito', $remision->iddeposito_origen)
                ->where('idproducto', $detalle->idproducto)
                ->lockForUpdate()
                ->first();

            if (! $stockOrigen || (float) $stockOrigen->cantidad < $cantidad) {
                $productoLabel = $producto
                    ? trim(($producto->codigo ? $producto->codigo . ' ' : '') . $producto->descripcion)
                    : 'Producto ' . $detalle->idproducto;

                throw new \RuntimeException('Stock insuficiente en deposito origen para ' . $productoLabel . '.');
            }

            DB::table('stock')
                ->where('iddeposito', $remision->iddeposito_origen)
                ->where('idproducto', $detalle->idproducto)
                ->update([
                    'cantidad' => DB::raw('cantidad - ' . $cantidad),
                    'idsucursal' => $depositoOrigen->idsucursal,
                ]);

            app(MovimientoStockService::class)->registrar(
                (int) $detalle->idproducto,
                (int) $depositoOrigen->idsucursal,
                (int) $remision->iddeposito_origen,
                'REMISION_VENTA',
                (int) $remision->idnota_remision_venta,
                'nota_remision_venta_detalle',
                'SALIDA',
                $cantidad,
                null,
                'Salida por remision entre depositos'
            );

            $stockDestino = DB::table('stock')
                ->where('iddeposito', $remision->iddeposito_destino)
                ->where('idproducto', $detalle->idproducto)
                ->lockForUpdate()
                ->first();

            if ($stockDestino) {
                DB::table('stock')
                    ->where('iddeposito', $remision->iddeposito_destino)
                    ->where('idproducto', $detalle->idproducto)
                    ->update([
                        'cantidad' => DB::raw('cantidad + ' . $cantidad),
                        'idsucursal' => $depositoDestino->idsucursal,
                    ]);
            } else {
                DB::table('stock')->insert([
                    'idsucursal' => $depositoDestino->idsucursal,
                    'iddeposito' => $remision->iddeposito_destino,
                    'idproducto' => $detalle->idproducto,
                    'cantidad' => $cantidad,
                ]);
            }

            app(MovimientoStockService::class)->registrar(
                (int) $detalle->idproducto,
                (int) $depositoDestino->idsucursal,
                (int) $remision->iddeposito_destino,
                'REMISION_VENTA',
                (int) $remision->idnota_remision_venta,
                'nota_remision_venta_detalle',
                'ENTRADA',
                $cantidad,
                null,
                'Entrada por remision entre depositos'
            );

            DB::table('nota_remision_stock_movimientos')->insert([
                'idnota_remision_venta' => $remision->idnota_remision_venta,
                'idproducto' => $detalle->idproducto,
                'iddeposito_origen' => $remision->iddeposito_origen,
                'iddeposito_destino' => $remision->iddeposito_destino,
                'cantidad' => $cantidad,
                'tipo_movimiento' => 'TRASLADO',
                'estado' => 'Aplicado',
                'idusuario' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function revertirStockTraslado(NotaRemisionVenta $remision): void
    {
        $movimientos = DB::table('nota_remision_stock_movimientos')
            ->where('idnota_remision_venta', $remision->idnota_remision_venta)
            ->where('estado', 'Aplicado')
            ->lockForUpdate()
            ->get();

        foreach ($movimientos as $movimiento) {
            $cantidad = (float) $movimiento->cantidad;
            $depositoOrigen = DB::table('depositos')->where('iddeposito', $movimiento->iddeposito_origen)->first();
            $depositoDestino = DB::table('depositos')->where('iddeposito', $movimiento->iddeposito_destino)->first();

            if (! $depositoOrigen || ! $depositoDestino) {
                throw new \RuntimeException('No se pudo identificar el deposito de origen o destino para revertir stock.');
            }

            $stockDestino = DB::table('stock')
                ->where('iddeposito', $movimiento->iddeposito_destino)
                ->where('idproducto', $movimiento->idproducto)
                ->lockForUpdate()
                ->first();

            if (! $stockDestino || (float) $stockDestino->cantidad < $cantidad) {
                throw new \RuntimeException('No se puede anular: el deposito destino ya no tiene stock suficiente para revertir el traslado.');
            }

            DB::table('stock')
                ->where('iddeposito', $movimiento->iddeposito_destino)
                ->where('idproducto', $movimiento->idproducto)
                ->update([
                    'cantidad' => DB::raw('cantidad - ' . $cantidad),
                    'idsucursal' => $depositoDestino->idsucursal,
                ]);

            app(MovimientoStockService::class)->registrar(
                (int) $movimiento->idproducto,
                (int) $depositoDestino->idsucursal,
                (int) $movimiento->iddeposito_destino,
                'REMISION_VENTA',
                (int) $remision->idnota_remision_venta,
                'nota_remision_venta_detalle',
                'SALIDA',
                $cantidad,
                null,
                'Reversion de entrada por anulacion de remision'
            );

            $stockOrigen = DB::table('stock')
                ->where('iddeposito', $movimiento->iddeposito_origen)
                ->where('idproducto', $movimiento->idproducto)
                ->lockForUpdate()
                ->first();

            if ($stockOrigen) {
                DB::table('stock')
                    ->where('iddeposito', $movimiento->iddeposito_origen)
                    ->where('idproducto', $movimiento->idproducto)
                    ->update([
                        'cantidad' => DB::raw('cantidad + ' . $cantidad),
                        'idsucursal' => $depositoOrigen->idsucursal,
                    ]);
            } else {
                DB::table('stock')->insert([
                    'idsucursal' => $depositoOrigen->idsucursal,
                    'iddeposito' => $movimiento->iddeposito_origen,
                    'idproducto' => $movimiento->idproducto,
                    'cantidad' => $cantidad,
                ]);
            }

            app(MovimientoStockService::class)->registrar(
                (int) $movimiento->idproducto,
                (int) $depositoOrigen->idsucursal,
                (int) $movimiento->iddeposito_origen,
                'REMISION_VENTA',
                (int) $remision->idnota_remision_venta,
                'nota_remision_venta_detalle',
                'ENTRADA',
                $cantidad,
                null,
                'Reversion de salida por anulacion de remision'
            );

            DB::table('nota_remision_stock_movimientos')
                ->where('idnota_remision_stock_movimiento', $movimiento->idnota_remision_stock_movimiento)
                ->update([
                    'estado' => 'Anulado',
                    'updated_at' => now(),
                ]);
        }
    }

    private function requiereMovimientoStock(NotaRemisionVenta $remision): bool
    {
        return is_null($remision->idventa)
            && $remision->motivo_traslado === 'Traslado entre locales de la misma empresa'
            && ! is_null($remision->iddeposito_origen)
            && ! is_null($remision->iddeposito_destino)
            && (int) $remision->iddeposito_origen !== (int) $remision->iddeposito_destino;
    }

    private function generarNumeroRemision(int $idremision, ?string $serie): string
    {
        $serie = $serie ?: '001-001';

        return $serie . '-' . str_pad((string) $idremision, 7, '0', STR_PAD_LEFT);
    }

    private function proximoIdTabla(string $tabla, string $pk): int
    {
        return ((int) DB::table($tabla)->max($pk)) + 1;
    }

    private function idsucursalDesdeDeposito(int $iddeposito): int
    {
        if ($iddeposito <= 0) {
            return 0;
        }

        return (int) DB::table('depositos')->where('iddeposito', $iddeposito)->value('idsucursal');
    }

    private function hashRemision(NotaRemisionVenta $remision, $detalles): string
    {
        return hash('sha256', json_encode([
            'idnota_remision_venta' => $remision->idnota_remision_venta,
            'idventa' => $remision->idventa,
            'tipo_origen' => $remision->tipo_origen,
            'nro_remision' => $remision->nro_remision,
            'fecha_emision' => $remision->fecha_emision,
            'fecha_inicio_traslado' => $remision->fecha_inicio_traslado,
            'fecha_fin_traslado' => $remision->fecha_fin_traslado,
            'motivo_traslado' => $remision->motivo_traslado,
            'idcliente' => $remision->idcliente,
            'iddestinatario_remision' => $remision->iddestinatario_remision,
            'iddeposito_origen' => $remision->iddeposito_origen,
            'iddeposito_destino' => $remision->iddeposito_destino,
            'idchofer' => $remision->idchofer,
            'idvehiculo' => $remision->idvehiculo,
            'idtransportista' => $remision->idtransportista,
            'idtimbrado' => $remision->idtimbrado,
            'detalles' => collect($detalles)->map(fn ($detalle) => [
                'idproducto' => $detalle->idproducto,
                'cantidad' => $detalle->cantidad,
            ])->values()->all(),
        ], JSON_UNESCAPED_UNICODE));
    }

    private function hashRecepcion(NotaRemisionVenta $remision): string
    {
        return hash('sha256', json_encode([
            'idnota_remision_venta' => $remision->idnota_remision_venta,
            'nro_remision' => $remision->nro_remision,
            'recibido_por' => $remision->recibido_por,
            'documento_receptor' => $remision->documento_receptor,
            'fecha_entrega' => $remision->fecha_entrega,
            'hora_entrega' => $remision->hora_entrega,
            'observacion_entrega' => $remision->observacion_entrega,
            'idusuario_recepcion' => $remision->idusuario_recepcion,
            'recepcion_registrada_at' => (string) $remision->recepcion_registrada_at,
        ], JSON_UNESCAPED_UNICODE));
    }

    private function estaAnulada(string $estado): bool
    {
        return in_array(strtoupper(trim($estado)), self::ESTADOS_ANULADOS, true);
    }
}
