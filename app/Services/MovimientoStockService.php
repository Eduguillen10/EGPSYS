<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class MovimientoStockService
{
    private const OPERACIONES = ['ENTRADA', 'SALIDA'];

    public function registrar(
        int $idproducto,
        int $idsucursal,
        int $iddeposito,
        string $tipoOrigen,
        int $idOrigen,
        ?string $detalleOrigen,
        string $operacion,
        float $cantidad,
        ?float $costoUnitario = null,
        ?string $observacion = null,
        ?int $idusuario = null,
        string $estado = 'ACTIVO'
    ): void {
        $operacion = strtoupper(trim($operacion));
        $tipoOrigen = strtoupper(trim($tipoOrigen));

        if (! in_array($operacion, self::OPERACIONES, true)) {
            throw new InvalidArgumentException('Operacion de stock invalida: ' . $operacion);
        }

        if ($cantidad <= 0) {
            throw new InvalidArgumentException('La cantidad del movimiento de stock debe ser mayor a cero.');
        }

        DB::table('movimiento_stock')->insert([
            'idproducto' => $idproducto,
            'idsucursal' => $idsucursal,
            'iddeposito' => $iddeposito,
            'fecha' => now(),
            'tipo_origen' => $tipoOrigen,
            'id_origen' => $idOrigen,
            'detalle_origen' => $detalleOrigen,
            'operacion' => $operacion,
            'cantidad' => $cantidad,
            'costo_unitario' => $costoUnitario,
            'observacion' => $observacion,
            'idusuario' => $idusuario ?: $this->usuarioActualId(),
            'estado' => $estado,
        ]);
    }

    private function usuarioActualId(): int
    {
        return (int) (Auth::id() ?? DB::table('users')->orderBy('id')->value('id') ?? 1);
    }
}
