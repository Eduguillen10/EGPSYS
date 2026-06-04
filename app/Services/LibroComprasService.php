<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LibroComprasService
{
    public function recalcular(int $idcompra): void
    {
        $compra = DB::table('compras')->where('idcompra', '=', $idcompra)->first();

        if (! $compra) {
            return;
        }

        $creditos = $this->totalesNotas('nota_credito_compra', 'montonota_credito_compra', $idcompra);
        $debitos = $this->totalesNotas('nota_debito_compra', 'montonota_debito_compra', $idcompra);

        DB::table('libro_compras')->updateOrInsert(
            ['idcompra' => $idcompra],
            [
                'idproveedor' => (int) $compra->idproveedor,
                'monto' => max(0, (int) $compra->montocompra + (int) $debitos->monto - (int) $creditos->monto),
                'montoexenta' => max(0, (int) $compra->montoexenta + (int) $debitos->montoexenta - (int) $creditos->montoexenta),
                'montoiva5' => max(0, (int) $compra->montoiva5 + (int) $debitos->montoiva5 - (int) $creditos->montoiva5),
                'montoiva10' => max(0, (int) $compra->montoiva10 + (int) $debitos->montoiva10 - (int) $creditos->montoiva10),
                'montogravada5' => max(0, (int) $compra->montogravada5 + (int) $debitos->montogravada5 - (int) $creditos->montogravada5),
                'montogravada10' => max(0, (int) $compra->montogravada10 + (int) $debitos->montogravada10 - (int) $creditos->montogravada10),
                'estado' => $this->estadoEsCancelado($compra->estado) ? 'Cancelado' : 'Realizado',
            ]
        );
    }

    private function totalesNotas(string $tabla, string $columnaMonto, int $idcompra): object
    {
        if (! Schema::hasTable($tabla)) {
            return (object) [
                'monto' => 0,
                'montoexenta' => 0,
                'montoiva5' => 0,
                'montoiva10' => 0,
                'montogravada5' => 0,
                'montogravada10' => 0,
            ];
        }

        return DB::table($tabla)
            ->where('idcompra', '=', $idcompra)
            ->whereNotIn('estado', ['Cancelado', 'Anulado', 'Anulada', 'A'])
            ->selectRaw("
                COALESCE(SUM({$columnaMonto}), 0) as monto,
                COALESCE(SUM(montoexenta), 0) as montoexenta,
                COALESCE(SUM(montoiva5), 0) as montoiva5,
                COALESCE(SUM(montoiva10), 0) as montoiva10,
                COALESCE(SUM(montogravada5), 0) as montogravada5,
                COALESCE(SUM(montogravada10), 0) as montogravada10
            ")
            ->first();
    }

    private function estadoEsCancelado(?string $estado): bool
    {
        return in_array(strtoupper(trim((string) $estado)), ['CANCELADO', 'CANCELADA', 'ANULADO', 'ANULADA', 'A'], true);
    }
}
