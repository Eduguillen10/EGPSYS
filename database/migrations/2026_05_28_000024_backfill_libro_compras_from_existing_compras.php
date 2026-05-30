<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('libro_compras') || ! Schema::hasTable('compras')) {
            return;
        }

        DB::statement("
            INSERT INTO libro_compras (idusuario, idsucursal, idcompra, total, totalexenta, totaliva5, totaliva10)
            SELECT
                COALESCE(u.id, (SELECT MIN(id) FROM users), 1),
                c.idsucursal,
                c.idcompra,
                COALESCE(c.totalcompra, 0),
                COALESCE(c.totalexenta, 0),
                COALESCE(c.totaliva5, 0),
                COALESCE(c.totaliva10, 0)
            FROM compras c
            LEFT JOIN users u ON u.name = c.usuario
            LEFT JOIN libro_compras lc ON lc.idcompra = c.idcompra
            WHERE lc.idcompra IS NULL
              AND COALESCE(c.totalcompra, 0) > 0
              AND UPPER(COALESCE(c.estado, '')) NOT IN ('CANCELADO', 'CANCELADA', 'ANULADO', 'ANULADA', 'A')
        ");
    }

    public function down(): void
    {
        // Backfill historico: no se elimina informacion contable al revertir la migracion.
    }
};
