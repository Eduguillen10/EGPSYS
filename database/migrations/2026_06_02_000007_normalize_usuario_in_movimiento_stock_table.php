<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('movimiento_stock')) {
            return;
        }

        if (! Schema::hasColumn('movimiento_stock', 'idusuario')) {
            Schema::table('movimiento_stock', function (Blueprint $table) {
                $table->unsignedBigInteger('idusuario')->nullable()->after('observacion');
            });
        }

        $fallbackUserId = (int) (DB::table('users')->orderBy('id')->value('id') ?? 1);

        if (Schema::hasColumn('movimiento_stock', 'usuario')) {
            DB::statement('
                UPDATE movimiento_stock ms
                LEFT JOIN users u ON u.name = ms.usuario
                SET ms.idusuario = COALESCE(u.id, ms.idusuario, ?)
                WHERE ms.idusuario IS NULL
            ', [$fallbackUserId]);
        }

        DB::table('movimiento_stock')
            ->whereNull('idusuario')
            ->update(['idusuario' => $fallbackUserId]);

        DB::statement('ALTER TABLE movimiento_stock MODIFY idusuario BIGINT(20) UNSIGNED NOT NULL');
        $this->addForeignIfMissing('movimiento_stock', 'fk_mov_stock_usuario', 'idusuario', 'users', 'id');

        if (Schema::hasColumn('movimiento_stock', 'usuario')) {
            Schema::table('movimiento_stock', function (Blueprint $table) {
                $table->dropColumn('usuario');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('movimiento_stock') || ! Schema::hasColumn('movimiento_stock', 'idusuario')) {
            return;
        }

        if (! Schema::hasColumn('movimiento_stock', 'usuario')) {
            Schema::table('movimiento_stock', function (Blueprint $table) {
                $table->string('usuario', 100)->nullable()->after('observacion');
            });
        }

        DB::statement('
            UPDATE movimiento_stock ms
            LEFT JOIN users u ON u.id = ms.idusuario
            SET ms.usuario = u.name
            WHERE ms.usuario IS NULL
        ');

        $this->dropForeignIfExists('movimiento_stock', 'fk_mov_stock_usuario');

        Schema::table('movimiento_stock', function (Blueprint $table) {
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
