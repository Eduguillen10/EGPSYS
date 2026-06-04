<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('usuario_sucursal')) {
            return;
        }

        $this->dropForeignIfExists('usuario_sucursal', 'usuario_sucursal_users_FK');

        if (Schema::hasColumn('usuario_sucursal', 'id') && ! Schema::hasColumn('usuario_sucursal', 'idusuario')) {
            DB::statement('ALTER TABLE usuario_sucursal CHANGE id idusuario BIGINT(20) UNSIGNED NOT NULL');
        }

        if (Schema::hasColumn('usuario_sucursal', 'idusuario')) {
            $this->addForeignIfMissing(
                'usuario_sucursal',
                'usuario_sucursal_idusuario_FK',
                'idusuario',
                'users',
                'id'
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('usuario_sucursal')) {
            return;
        }

        $this->dropForeignIfExists('usuario_sucursal', 'usuario_sucursal_idusuario_FK');

        if (Schema::hasColumn('usuario_sucursal', 'idusuario') && ! Schema::hasColumn('usuario_sucursal', 'id')) {
            DB::statement('ALTER TABLE usuario_sucursal CHANGE idusuario id BIGINT(20) UNSIGNED NOT NULL');
        }

        if (Schema::hasColumn('usuario_sucursal', 'id')) {
            $this->addForeignIfMissing(
                'usuario_sucursal',
                'usuario_sucursal_users_FK',
                'id',
                'users',
                'id'
            );
        }
    }

    private function addForeignIfMissing(
        string $table,
        string $constraint,
        string $column,
        string $referencedTable,
        string $referencedColumn
    ): void {
        $exists = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->exists();

        if (! $exists) {
            DB::statement(
                "ALTER TABLE {$table} ADD CONSTRAINT {$constraint} FOREIGN KEY ({$column}) REFERENCES {$referencedTable}({$referencedColumn})"
            );
        }
    }

    private function dropForeignIfExists(string $table, string $constraint): void
    {
        $exists = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->exists();

        if ($exists) {
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$constraint}");
        }
    }
};
