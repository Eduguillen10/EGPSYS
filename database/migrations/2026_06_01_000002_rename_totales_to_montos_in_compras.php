<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('compras', 'totaliva10')) {
            DB::statement('ALTER TABLE compras CHANGE totaliva10 montoiva10 INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('compras', 'totaliva5')) {
            DB::statement('ALTER TABLE compras CHANGE totaliva5 montoiva5 INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('compras', 'totalgravada10')) {
            DB::statement('ALTER TABLE compras CHANGE totalgravada10 montogravada10 INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('compras', 'totalgravada5')) {
            DB::statement('ALTER TABLE compras CHANGE totalgravada5 montogravada5 INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('compras', 'totalexenta')) {
            DB::statement('ALTER TABLE compras CHANGE totalexenta montoexenta INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('compras', 'totalcompra')) {
            DB::statement('ALTER TABLE compras CHANGE totalcompra montocompra INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('compra_detalle', 'totalitems')) {
            DB::statement('ALTER TABLE compra_detalle CHANGE totalitems montoitems INT(11) NULL');
        }

        if (Schema::hasColumn('libro_compras', 'total')) {
            DB::statement('ALTER TABLE libro_compras CHANGE total monto INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('libro_compras', 'totalexenta')) {
            DB::statement('ALTER TABLE libro_compras CHANGE totalexenta montoexenta INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('libro_compras', 'totaliva5')) {
            DB::statement('ALTER TABLE libro_compras CHANGE totaliva5 montoiva5 INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('libro_compras', 'totaliva10')) {
            DB::statement('ALTER TABLE libro_compras CHANGE totaliva10 montoiva10 INT(10) UNSIGNED NULL');
        }

        if (! Schema::hasColumn('libro_compras', 'montogravada5')) {
            DB::statement('ALTER TABLE libro_compras ADD montogravada5 INT(10) UNSIGNED NULL AFTER montoiva10');
        }

        if (! Schema::hasColumn('libro_compras', 'montogravada10')) {
            DB::statement('ALTER TABLE libro_compras ADD montogravada10 INT(10) UNSIGNED NULL AFTER montogravada5');
        }

        if (Schema::hasColumn('libro_compras', 'idusuario')) {
            DB::statement('ALTER TABLE libro_compras MODIFY idusuario BIGINT(20) UNSIGNED NOT NULL');
        }

        if (Schema::hasColumn('compras', 'montogravada5') && Schema::hasColumn('libro_compras', 'montogravada5')) {
            DB::statement('
                UPDATE libro_compras lc
                JOIN compras c ON c.idcompra = lc.idcompra
                SET lc.montogravada5 = COALESCE(c.montogravada5, 0),
                    lc.montogravada10 = COALESCE(c.montogravada10, 0)
            ');
        }

        $this->addForeignIfMissing('libro_compras', 'fk_libro_compras_usuario', 'idusuario', 'users', 'id');
        $this->addForeignIfMissing('libro_compras', 'fk_libro_compras_sucursal', 'idsucursal', 'sucursales', 'idsucursal');
        $this->addForeignIfMissing('libro_compras', 'fk_libro_compras_compra', 'idcompra', 'compras', 'idcompra');
    }

    public function down(): void
    {
        $this->dropForeignIfExists('libro_compras', 'fk_libro_compras_usuario');
        $this->dropForeignIfExists('libro_compras', 'fk_libro_compras_sucursal');
        $this->dropForeignIfExists('libro_compras', 'fk_libro_compras_compra');

        if (Schema::hasColumn('libro_compras', 'montogravada10')) {
            DB::statement('ALTER TABLE libro_compras DROP COLUMN montogravada10');
        }

        if (Schema::hasColumn('libro_compras', 'montogravada5')) {
            DB::statement('ALTER TABLE libro_compras DROP COLUMN montogravada5');
        }

        if (Schema::hasColumn('libro_compras', 'montoiva10')) {
            DB::statement('ALTER TABLE libro_compras CHANGE montoiva10 totaliva10 INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('libro_compras', 'montoiva5')) {
            DB::statement('ALTER TABLE libro_compras CHANGE montoiva5 totaliva5 INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('libro_compras', 'montoexenta')) {
            DB::statement('ALTER TABLE libro_compras CHANGE montoexenta totalexenta INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('libro_compras', 'monto')) {
            DB::statement('ALTER TABLE libro_compras CHANGE monto total INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('compra_detalle', 'montoitems')) {
            DB::statement('ALTER TABLE compra_detalle CHANGE montoitems totalitems INT(11) NULL');
        }

        if (Schema::hasColumn('compras', 'montocompra')) {
            DB::statement('ALTER TABLE compras CHANGE montocompra totalcompra INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('compras', 'montoexenta')) {
            DB::statement('ALTER TABLE compras CHANGE montoexenta totalexenta INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('compras', 'montogravada5')) {
            DB::statement('ALTER TABLE compras CHANGE montogravada5 totalgravada5 INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('compras', 'montogravada10')) {
            DB::statement('ALTER TABLE compras CHANGE montogravada10 totalgravada10 INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('compras', 'montoiva5')) {
            DB::statement('ALTER TABLE compras CHANGE montoiva5 totaliva5 INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('compras', 'montoiva10')) {
            DB::statement('ALTER TABLE compras CHANGE montoiva10 totaliva10 INT(10) UNSIGNED NULL');
        }

        if (Schema::hasColumn('libro_compras', 'idusuario')) {
            DB::statement('ALTER TABLE libro_compras MODIFY idusuario INT(10) UNSIGNED NOT NULL');
        }
    }

    private function addForeignIfMissing(string $table, string $constraint, string $column, string $foreignTable, string $foreignColumn): void
    {
        $exists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
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
