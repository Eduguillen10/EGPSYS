<?php

namespace App\Http\Controllers;

use App\Models\VentaCreditoAceptacion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\LegalDocumentHashService;

class VentaCreditoAceptacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(int $idventa)
    {
        $venta = $this->ventaCredito($idventa);
        $aceptacion = $this->aceptacionVigente($idventa);

        if ($aceptacion) {
            return redirect()
                ->route('venta_credito_aceptacion.show', [$idventa, $aceptacion->idaceptacion_credito])
                ->with('info', 'Esta venta ya tiene aceptacion de credito registrada.');
        }

        return view('ventas.credito_aceptacion.create', [
            'venta' => $venta,
            'cuenta' => $this->cuentaCobrar($idventa),
            'textoAceptado' => $this->textoAceptado($venta, $this->cuentaCobrar($idventa)),
        ]);
    }

    public function store(Request $request, int $idventa)
    {
        $venta = $this->ventaCredito($idventa);
        $cuenta = $this->cuentaCobrar($idventa);

        if ($this->aceptacionVigente($idventa)) {
            return redirect()
                ->route('venta.show', $idventa)
                ->with('info', 'Esta venta ya cuenta con respaldo de aceptacion.');
        }

        $request->validate([
            'recibido_por' => ['required', 'string', 'max:150'],
            'documento_receptor' => ['required', 'string', 'max:50'],
            'telefono_receptor' => ['nullable', 'string', 'max:50'],
            'relacion_receptor' => ['nullable', 'string', 'max:80'],
            'archivo_respaldo' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'observacion' => ['nullable', 'string', 'max:255'],
        ], [
            'archivo_respaldo.required' => 'Debe adjuntar la firma fisica escaneada o fotografiada.',
            'archivo_respaldo.mimes' => 'El respaldo debe ser una imagen JPG/PNG o un PDF.',
        ]);

        $textoAceptado = $this->textoAceptado($venta, $cuenta);
        $archivo = $this->guardarArchivo($request->file('archivo_respaldo'), $idventa);
        $hash = hash('sha256', implode('|', [
            $idventa,
            $venta->idcliente,
            $request->input('recibido_por'),
            $request->input('documento_receptor'),
            (int) $venta->totalventa,
            $venta->condicion,
            $cuenta?->fecha_vencimiento,
            $textoAceptado,
            hash_file('sha256', public_path($archivo['ruta'])),
        ]));

        $aceptacion = VentaCreditoAceptacion::create([
            'idventa' => $idventa,
            'idcliente' => (int) $venta->idcliente,
            'metodo_aceptacion' => 'Firma fisica',
            'recibido_por' => $request->input('recibido_por'),
            'documento_receptor' => $request->input('documento_receptor'),
            'telefono_receptor' => $request->input('telefono_receptor'),
            'relacion_receptor' => $request->input('relacion_receptor'),
            'monto' => (int) $venta->totalventa,
            'condicion' => $venta->condicion,
            'fecha_vencimiento' => $cuenta?->fecha_vencimiento,
            'texto_aceptado' => $textoAceptado,
            'archivo_respaldo' => $archivo['ruta'],
            'archivo_nombre_original' => $archivo['original'],
            'hash_documento' => $hash,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'estado' => 'Aceptado',
            'idusuario' => Auth::id(),
            'observacion' => $request->input('observacion'),
        ]);

        $hashService = app(LegalDocumentHashService::class);
        $aceptacion->forceFill([
            'hash_documento' => $hashService->hashAceptacionCredito((int) $aceptacion->idaceptacion_credito),
            'hash_version' => LegalDocumentHashService::VERSION,
        ])->save();

        return redirect()
            ->route('venta_credito_aceptacion.show', [$idventa, $aceptacion->idaceptacion_credito])
            ->with('success', 'Aceptacion fisica de credito registrada correctamente.');
    }

    public function show(int $idventa, int $idaceptacion)
    {
        $venta = $this->ventaCredito($idventa);
        $aceptacion = DB::table('venta_credito_aceptaciones as a')
            ->join('users as u', 'a.idusuario', '=', 'u.id')
            ->select('a.*', 'u.name as usuario')
            ->where('a.idventa', $idventa)
            ->where('a.idaceptacion_credito', $idaceptacion)
            ->first();

        if (! $aceptacion) {
            abort(404, 'Aceptacion de credito no encontrada.');
        }

        return view('ventas.credito_aceptacion.show', [
            'venta' => $venta,
            'cuenta' => $this->cuentaCobrar($idventa),
            'aceptacion' => $aceptacion,
            'hashValido' => app(LegalDocumentHashService::class)->verificar(
                $aceptacion->hash_documento,
                app(LegalDocumentHashService::class)->hashAceptacionCredito((int) $aceptacion->idaceptacion_credito)
            ),
        ]);
    }

    public function plantilla(int $idventa)
    {
        $venta = $this->ventaCredito($idventa);

        return view('ventas.credito_aceptacion.plantilla', [
            'venta' => $venta,
            'cuenta' => $this->cuentaCobrar($idventa),
            'textoAceptado' => $this->textoAceptado($venta, $this->cuentaCobrar($idventa)),
        ]);
    }

    public function comprobante(int $idventa, int $idaceptacion)
    {
        $venta = $this->ventaCredito($idventa);
        $aceptacion = DB::table('venta_credito_aceptaciones as a')
            ->join('users as u', 'a.idusuario', '=', 'u.id')
            ->select('a.*', 'u.name as usuario')
            ->where('a.idventa', $idventa)
            ->where('a.idaceptacion_credito', $idaceptacion)
            ->first();

        if (! $aceptacion) {
            abort(404, 'Aceptacion de credito no encontrada.');
        }

        return view('ventas.credito_aceptacion.comprobante', [
            'venta' => $venta,
            'cuenta' => $this->cuentaCobrar($idventa),
            'aceptacion' => $aceptacion,
            'hashValido' => app(LegalDocumentHashService::class)->verificar(
                $aceptacion->hash_documento,
                app(LegalDocumentHashService::class)->hashAceptacionCredito((int) $aceptacion->idaceptacion_credito)
            ),
        ]);
    }

    private function ventaCredito(int $idventa): object
    {
        $venta = DB::table('ventas as v')
            ->join('clientes as c', 'v.idcliente', '=', 'c.idcliente')
            ->leftJoin('sucursales as s', 'v.idsucursal', '=', 's.idsucursal')
            ->select(
                'v.*',
                'c.nombre as cliente',
                'c.num_documento as cliente_documento',
                'c.direccion as cliente_direccion',
                'c.telefono as cliente_telefono',
                'c.email as cliente_email',
                's.descripcion as sucursal'
            )
            ->where('v.idventa', $idventa)
            ->first();

        if (! $venta) {
            abort(404, 'Venta no encontrada.');
        }

        if (mb_strtolower(trim((string) $venta->condicion)) === 'contado') {
            abort(422, 'La aceptacion de credito solo aplica a ventas a credito.');
        }

        if (in_array(strtoupper(trim((string) $venta->estado)), ['A', 'ANULADO', 'ANULADA', 'CANCELADO', 'CANCELADA'], true)) {
            abort(422, 'No se puede respaldar una venta anulada.');
        }

        return $venta;
    }

    private function cuentaCobrar(int $idventa): ?object
    {
        return DB::table('cuenta_cobrar')->where('idventa', $idventa)->first();
    }

    private function aceptacionVigente(int $idventa): ?VentaCreditoAceptacion
    {
        return VentaCreditoAceptacion::where('idventa', $idventa)
            ->where('estado', 'Aceptado')
            ->orderByDesc('idaceptacion_credito')
            ->first();
    }

    private function textoAceptado(object $venta, ?object $cuenta): string
    {
        $fechaVencimiento = $cuenta?->fecha_vencimiento
            ? Carbon::parse($cuenta->fecha_vencimiento)->format('d/m/Y')
            : 'segun condicion pactada';

        return 'Declaro haber recibido conforme los bienes y/o servicios detallados en la factura Nro. '
            . $venta->nro_factura
            . ', reconozco la deuda por Gs. '
            . number_format((int) $venta->totalventa, 0, ',', '.')
            . ' y me comprometo a pagarla al vencimiento '
            . $fechaVencimiento
            . '.';
    }

    private function guardarArchivo($file, int $idventa): array
    {
        $directorio = 'documentos/ventas/aceptaciones';
        $destino = public_path($directorio);

        if (! is_dir($destino)) {
            mkdir($destino, 0755, true);
        }

        $nombre = now()->format('YmdHis') . '_venta_' . $idventa . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move($destino, $nombre);

        return [
            'ruta' => $directorio . '/' . $nombre,
            'original' => $file->getClientOriginalName(),
        ];
    }
}
