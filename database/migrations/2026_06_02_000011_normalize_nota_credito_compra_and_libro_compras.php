<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->normalizarNotaCreditoCompra();
        $this->normalizarLibroCompras();
    }

    public function down(): void
    {
        $this->dropForeignIfExists('nota_credito_compra_detalle', 'fk_nc_compra_detalle_cabecera');
        $this->dropForeignIfExists('nota_credito_compra_detalle', 'fk_nc_compra_detalle_producto');
        $this->dropForeignIfExists('nota_credito_compra', 'fk_nc_compra_compra');
        $this->dropForeignIfExists('nota_credito_compra', 'fk_nc_compra_sucursal');
        $this->dropForeignIfExists('nota_credito_compra', 'fk_nc_compra_deposito');
        $this->dropForeignIfExists('nota_credito_compra', 'fk_nc_compra_proveedor');
        $this->dropForeignIfExists('nota_credito_compra', 'fk_nc_compra_usuario');

        $this->dropForeignIfExists('libro_compras', 'fk_libro_compras_proveedor');

        if (Schema::hasColumn('libro_compras', 'estado')) {
            DB::statement('ALTER TABLE libro_compras DROP COLUMN estado');
        }

        if (Schema::hasColumn('libro_compras', 'idproveedor')) {
            DB::statement('ALTER TABLE libro_compras DROP COLUMN idproveedor');
        }

        if (! Schema::hasColumn('libro_compras', 'idusuario')) {
            DB::statement('ALTER TABLE libro_compras ADD idusuario BIGINT(20) UNSIGNED NOT NULL AFTER idlibrocompra');
        }

        if (! Schema::hasColumn('libro_compras', 'idsucursal')) {
            DB::statement('ALTER TABLE libro_compras ADD idsucursal INT(10) UNSIGNED NOT NULL AFTER idusuario');
        }

        if (Schema::hasColumn('nota_credito_compra_detalle', 'montoitems')) {
            DB::statement('ALTER TABLE nota_credito_compra_detalle CHANGE montoitems totalitems INT(11) NULL');
        }

        foreach ([
            'montoiva10' => 'totaliva10',
            'montoiva5' => 'totaliva5',
            'montogravada10' => 'totalgravada10',
            'montogravada5' => 'totalgravada5',
            'montoexenta' => 'totalexenta',
            'montonota_credito_compra' => 'totalcompra',
        ] as $from => $to) {
            if (Schema::hasColumn('nota_credito_compra', $from)) {
                DB::statement("ALTER TABLE nota_credito_compra CHANGE {$from} {$to} INT(11) NULL");
            }
        }
    }

    private function normalizarNotaCreditoCompra(): void
    {
        if (! Schema::hasTable('nota_credito_compra')) {
            return;
        }

        foreach ([
            'totaliva10' => 'montoiva10',
            'totaliva5' => 'montoiva5',
            'totalgravada10' => 'montogravada10',
            'totalgravada5' => 'montogravada5',
            'totalexenta' => 'montoexenta',
            'totalcompra' => 'montonota_credito_compra',
        ] as $from => $to) {
            if (Schema::hasColumn('nota_credito_compra', $from) && ! Schema::hasColumn('nota_credito_compra', $to)) {
                DB::statement("ALTER TABLE nota_credito_compra CHANGE {$from} {$to} INT(11) NULL");
            }
        }

        DB::table('nota_credito_compra')
            ->where('estado', 'R')
            ->update(['estado' => 'Realizado']);

        DB::table('nota_credito_compra')
            ->whereIn('estado', ['A', 'Anulado', 'Anulada'])
            ->update(['estado' => 'Cancelado']);

        $this->addForeignIfMissing('nota_credito_compra', 'fk_nc_compra_compra', 'idcompra', 'compras', 'idcompra');
        $this->addForeignIfMissing('nota_credito_compra', 'fk_nc_compra_sucursal', 'idsucursal', 'sucursales', 'idsucursal');
        $this->addForeignIfMissing('nota_credito_compra', 'fk_nc_compra_deposito', 'iddeposito', 'depositos', 'iddeposito');
        $this->addForeignIfMissing('nota_credito_compra', 'fk_nc_compra_proveedor', 'idproveedor', 'proveedores', 'idproveedor');
        $this->addForeignIfMissing('nota_credito_compra', 'fk_nc_compra_usuario', 'idusuario', 'users', 'id');

        if (Schema::hasColumn('nota_credito_compra_detalle', 'totalitems') && ! Schema::hasColumn('nota_credito_compra_detalle', 'montoitems')) {
            DB::statement('ALTER TABLE nota_credito_compra_detalle CHANGE totalitems montoitems INT(11) NULL');
        }

        $this->addForeignIfMissing('nota_credito_compra_detalle', 'fk_nc_compra_detalle_cabecera', 'idnota_creditoc', 'nota_credito_compra', 'idnota_creditoc');
        $this->addForeignIfMissing('nota_credito_compra_detalle', 'fk_nc_compra_detalle_producto', 'idproducto', 'productos', 'idproducto');
    }

    private function normalizarLibroCompras(): void
    {
        if (! Schema::hasTable('libro_compras')) {
            return;
        }

        $this->dropForeignIfExists('libro_compras', 'fk_libro_compras_usuario');
        $this->dropForeignIfExists('libro_compras', 'fk_libro_compras_sucursal');

        if (! Schema::hasColumn('libro_compras', 'idproveedor')) {
            DB::statement('ALTER TABLE libro_compras ADD idproveedor INT(10) UNSIGNED NULL AFTER idcompra');
        }

        DB::statement('
            UPDATE libro_compras lc
            JOIN compras c ON c.idcompra = lc.idcompra
            SET lc.idproveedor = c.idproveedor
            WHERE lc.idproveedor IS NULL
        ');

        DB::statement('ALTER TABLE libro_compras MODIFY idproveedor INT(10) UNSIGNED NOT NULL');

        if (! Schema::hasColumn('libro_compras', 'estado')) {
            DB::statement("ALTER TABLE libro_compras ADD estado VARCHAR(20) NOT NULL DEFAULT 'Realizado' AFTER montogravada10");
        }

        DB::statement("
            UPDATE libro_compras lc
            JOIN compras c ON c.idcompra = lc.idcompra
            SET lc.estado = CASE
                WHEN UPPER(COALESCE(c.estado, '')) IN ('CANCELADO', 'CANCELADA', 'ANULADO', 'ANULADA', 'A') THEN 'Cancelado'
                ELSE 'Realizado'
            END
        ");

        if (Schema::hasColumn('libro_compras', 'idusuario')) {
            DB::statement('ALTER TABLE libro_compras DROP COLUMN idusuario');
        }

        if (Schema::hasColumn('libro_compras', 'idsucursal')) {
            DB::statement('ALTER TABLE libro_compras DROP COLUMN idsucursal');
        }

        $this->addForeignIfMissing('libro_compras', 'fk_libro_compras_compra', 'idcompra', 'compras', 'idcompra');
        $this->addForeignIfMissing('libro_compras', 'fk_libro_compras_proveedor', 'idproveedor', 'proveedores', 'idproveedor');
    }

    private function addForeignIfMissing(string $table, string $constraint, string $column, string $foreignTable, string $foreignColumn): void
    {
        if (! Schema::hasColumn($table, $column)) {
            return;
        }

        $exists = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->where('REFERENCED_TABLE_NAME', $foreignTable)
            ->where('REFERENCED_COLUMN_NAME', $foreignColumn)
            ->exists();

        if (! $exists) {
            DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$constraint} FOREIGN KEY ({$column}) REFERENCES {$foreignTable}({$foreignColumn})");
        }
    }

    private function dropForeignIfExists(string $table, string $constraint): void
    {
        $exists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->exists();

        if ($exists) {
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$constraint}");
        }
    }
};
