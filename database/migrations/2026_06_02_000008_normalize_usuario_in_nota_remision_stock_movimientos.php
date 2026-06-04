<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('nota_remision_stock_movimientos')) {
            return;
        }

        if (! Schema::hasColumn('nota_remision_stock_movimientos', 'idusuario')) {
            Schema::table('nota_remision_stock_movimientos', function (Blueprint $table) {
                $table->unsignedBigInteger('idusuario')->nullable()->after('estado');
            });
        }

        $fallbackUserId = (int) (DB::table('users')->orderBy('id')->value('id') ?? 1);

        if (Schema::hasColumn('nota_remision_stock_movimientos', 'usuario')) {
            DB::statement('
                UPDATE nota_remision_stock_movimientos nrs
                LEFT JOIN users u ON u.name = nrs.usuario
                SET nrs.idusuario = COALESCE(u.id, nrs.idusuario, ?)
                WHERE nrs.idusuario IS NULL
            ', [$fallbackUserId]);
        }

        DB::table('nota_remision_stock_movimientos')
            ->whereNull('idusuario')
            ->update(['idusuario' => $fallbackUserId]);

        DB::statement('ALTER TABLE nota_remision_stock_movimientos MODIFY idusuario BIGINT(20) UNSIGNED NOT NULL');
        $this->addForeignIfMissing('nota_remision_stock_movimientos', 'nrsm_usuario_fk', 'idusuario', 'users', 'id');

        if (Schema::hasColumn('nota_remision_stock_movimientos', 'usuario')) {
            Schema::table('nota_remision_stock_movimientos', function (Blueprint $table) {
                $table->dropColumn('usuario');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('nota_remision_stock_movimientos') || ! Schema::hasColumn('nota_remision_stock_movimientos', 'idusuario')) {
            return;
        }

        if (! Schema::hasColumn('nota_remision_stock_movimientos', 'usuario')) {
            Schema::table('nota_remision_stock_movimientos', function (Blueprint $table) {
                $table->string('usuario', 100)->nullable()->after('estado');
            });
        }

        DB::statement('
            UPDATE nota_remision_stock_movimientos nrs
            LEFT JOIN users u ON u.id = nrs.idusuario
            SET nrs.usuario = u.name
            WHERE nrs.usuario IS NULL
        ');

        $this->dropForeignIfExists('nota_remision_stock_movimientos', 'nrsm_usuario_fk');

        Schema::table('nota_remision_stock_movimientos', function (Blueprint $table) {
            $table->dropColumn('idusuario');
        });
    }

    private function addForeignIfMissing(string $table, string $constraint, string $column, string $referencedTable, string $referencedColumn): void
    {
        $schema = DB::getDatabaseName();
        $exists = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $schema)
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->exists();

        if (! $exists) {
            DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$constraint} FOREIGN KEY ({$column}) REFERENCES {$referencedTable}({$referencedColumn})");
        }
    }

    private function dropForeignIfExists(string $table, string $constraint): void
    {
        $schema = DB::getDatabaseName();
        $exists = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $schema)
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->exists();

        if ($exists) {
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$constraint}");
        }
    }
};
