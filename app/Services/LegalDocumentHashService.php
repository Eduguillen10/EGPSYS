<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class LegalDocumentHashService
{
    public const VERSION = 'ventas-v1';

    public function hashVenta(int $idventa): string
    {
        $venta = DB::table('ventas')->where('idventa', $idventa)->first();
        $detalles = DB::table('venta_detalle')
            ->where('idventa', $idventa)
            ->orderBy('items')
            ->get();

        return $this->hash([
            'tipo' => 'VENTA',
            'cabecera' => $this->only($venta, [
                'idventa', 'idsucursal', 'iddeposito', 'idtimbrado', 'idcliente',
                'fecha', 'nro_factura', 'condicion', 'totaliva10', 'totaliva5',
                'totalgravada10', 'totalgravada5', 'totalexenta', 'totalventa',
                'saldo_factura', 'estado', 'idusuario',
            ]),
            'detalles' => $this->rows($detalles, [
                'items', 'idproducto', 'cantidad', 'precio_venta', 'iva10', 'iva5',
                'gravada10', 'gravada5', 'exenta', 'totalitems',
            ]),
        ]);
    }

    public function hashCobro(int $idCobro): string
    {
        $cobro = DB::table('cobros')->where('id_cobro', $idCobro)->first();
        $detalles = DB::table('det_cobro')
            ->where('id_cobro', $idCobro)
            ->orderBy('items')
            ->get();
        $formas = DB::table('det_formacobro')
            ->where('id_cobro', $idCobro)
            ->orderBy('items')
            ->get();

        return $this->hash([
            'tipo' => 'COBRO',
            'cabecera' => $this->only($cobro, [
                'id_cobro', 'idsucursal', 'idcliente', 'idcaja', 'idapertura',
                'fecha_cobro', 'monto_cobro', 'cobro_estado', 'idusuario',
            ]),
            'facturas' => $this->rows($detalles, [
                'items', 'idventa', 'monto_detcobro',
            ]),
            'formas_cobro' => $this->rows($formas, [
                'items', 'id_formacobro', 'identidademisora', 'documento',
                'fecha', 'fecha_vencimiento', 'monto_detformacobro',
                'monto_recibido', 'vuelto', 'idtipodocumento',
            ]),
        ]);
    }

    public function hashNotaCredito(int $idNota): string
    {
        return $this->hashNotaVenta('NOTA_CREDITO_VENTA', 'nota_credito_venta', 'idnota_creditov', 'nota_credito_venta_detalle', $idNota);
    }

    public function hashNotaDebito(int $idNota): string
    {
        return $this->hashNotaVenta('NOTA_DEBITO_VENTA', 'nota_debito_venta', 'idnota_debitov', 'nota_debito_venta_detalle', $idNota);
    }

    public function hashNotaRemision(int $idRemision, bool $recepcion = false): string
    {
        $remision = DB::table('nota_remision_venta')->where('idnota_remision_venta', $idRemision)->first();
        $detalles = DB::table('nota_remision_venta_detalle')
            ->where('idnota_remision_venta', $idRemision)
            ->orderBy('items')
            ->get();

        $campos = [
            'idnota_remision_venta', 'nro_remision', 'idventa', 'idcliente',
            'iddestinatario_remision', 'tipo_origen', 'idchofer', 'idvehiculo',
            'idtimbrado', 'idtransportista', 'iddeposito_origen', 'iddeposito_destino',
            'fecha_emision', 'fecha_inicio_traslado', 'fecha_fin_traslado',
            'motivo_traslado', 'punto_partida', 'ciudad_partida',
            'departamento_partida', 'punto_llegada', 'ciudad_llegada',
            'departamento_llegada', 'estado', 'idusuario', 'observacion',
        ];

        if ($recepcion) {
            $campos = array_merge($campos, [
                'recibido_por', 'documento_receptor', 'fecha_entrega',
                'hora_entrega', 'observacion_entrega', 'idusuario_recepcion',
                'recepcion_registrada_at',
            ]);
        }

        return $this->hash([
            'tipo' => $recepcion ? 'NOTA_REMISION_RECEPCION' : 'NOTA_REMISION',
            'cabecera' => $this->only($remision, $campos),
            'detalles' => $this->rows($detalles, [
                'items', 'idproducto', 'cantidad',
            ]),
        ]);
    }

    public function hashAceptacionCredito(int $idAceptacion): string
    {
        $aceptacion = DB::table('venta_credito_aceptaciones')
            ->where('idaceptacion_credito', $idAceptacion)
            ->first();

        $archivoHash = null;
        if ($aceptacion?->archivo_respaldo) {
            $path = public_path($aceptacion->archivo_respaldo);
            $archivoHash = is_file($path) ? hash_file('sha256', $path) : null;
        }

        return $this->hash([
            'tipo' => 'ACEPTACION_CREDITO',
            'cabecera' => $this->only($aceptacion, [
                'idaceptacion_credito', 'idventa', 'idcliente', 'metodo_aceptacion',
                'recibido_por', 'documento_receptor', 'telefono_receptor',
                'relacion_receptor', 'monto', 'condicion', 'fecha_vencimiento',
                'texto_aceptado', 'archivo_nombre_original', 'estado', 'idusuario',
                'observacion',
            ]),
            'archivo_sha256' => $archivoHash,
        ]);
    }

    public function hashAnulacion(string $tipo, int $id, ?string $motivo, ?string $usuario): string
    {
        return $this->hash([
            'tipo' => $tipo . '_ANULACION',
            'id' => $id,
            'motivo' => $motivo,
            'usuario' => $usuario,
            'fecha' => now()->toDateTimeString(),
        ]);
    }

    public function verificar(?string $hashGuardado, string $hashCalculado): bool
    {
        return is_string($hashGuardado)
            && $hashGuardado !== ''
            && hash_equals($hashGuardado, $hashCalculado);
    }

    private function hashNotaVenta(string $tipo, string $tabla, string $pk, string $tablaDetalle, int $id): string
    {
        $cabecera = DB::table($tabla)->where($pk, $id)->first();
        $detalles = DB::table($tablaDetalle)->where($pk, $id)->orderBy('items')->get();

        return $this->hash([
            'tipo' => $tipo,
            'cabecera' => $this->only($cabecera, [
                $pk, 'idventa', 'idsucursal', 'idcliente', 'iddeposito',
                'num_documento', 'nro_nota_credito', 'nro_nota_debito', 'nro_factura', 'condicion', 'fecha_registro',
                'concepto', 'totaliva10', 'totaliva5', 'totalgravada10',
                'totalgravada5', 'totalexenta', 'totalventa', 'estado',
                'timbrado', 'idusuario', 'fecha_factura', 'fecha_vencimiento',
            ]),
            'detalles' => $this->rows($detalles, [
                'items', 'idproducto', 'cantidad', 'precio_venta', 'iva10', 'iva5',
                'gravada10', 'gravada5', 'exenta', 'totalitems',
            ]),
        ]);
    }

    private function hash(array $payload): string
    {
        $payload['hash_version'] = self::VERSION;
        $this->ksortRecursive($payload);

        return hash('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function only(?object $row, array $fields): array
    {
        if (! $row) {
            return [];
        }

        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $row->{$field} ?? null;
        }

        return $data;
    }

    private function rows($rows, array $fields): array
    {
        return collect($rows)
            ->map(fn ($row) => $this->only($row, $fields))
            ->values()
            ->all();
    }

    private function ksortRecursive(array &$array): void
    {
        ksort($array);

        foreach ($array as &$value) {
            if (is_array($value)) {
                $this->ksortRecursive($value);
            }
        }
    }
}
