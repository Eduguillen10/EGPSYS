<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cajas')) {
            return;
        }

        $this->dropForeignIfExists('cajas', 'cajas_users_FK');
        $this->dropForeignIfExists('cajas', 'fk_cajas_usuario');

        if (Schema::hasColumn('cajas', 'id') && ! Schema::hasColumn('cajas', 'idusuario')) {
            DB::statement('ALTER TABLE cajas CHANGE id idusuario BIGINT(20) UNSIGNED NOT NULL');
        }

        $this->addForeignIfMissing('cajas', 'fk_cajas_usuario', 'idusuario', 'users', 'id');
    }

    public function down(): void
    {
        if (! Schema::hasTable('cajas')) {
            return;
        }

        $this->dropForeignIfExists('cajas', 'fk_cajas_usuario');

        if (Schema::hasColumn('cajas', 'idusuario') && ! Schema::hasColumn('cajas', 'id')) {
            DB::statement('ALTER TABLE cajas CHANGE idusuario id BIGINT(20) UNSIGNED NOT NULL');
        }

        $this->addForeignIfMissing('cajas', 'cajas_users_FK', 'id', 'users', 'id');
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
