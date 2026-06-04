<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->renameUserColumn('audit_logs', 'audit_logs_user_id_foreign', 'audit_logs_idusuario_foreign', true);
        $this->renameUserColumn('login_attempts', 'login_attempts_user_id_foreign', 'login_attempts_idusuario_foreign', true);

        if (Schema::hasTable('usuario_permisos')) {
            $this->dropForeignIfExists('usuario_permisos', 'usuario_permisos_user_id_foreign');
            $this->dropIndexIfExists('usuario_permisos', 'usuario_permisos_unique');

            if (Schema::hasColumn('usuario_permisos', 'user_id') && ! Schema::hasColumn('usuario_permisos', 'idusuario')) {
                DB::statement('ALTER TABLE usuario_permisos CHANGE user_id idusuario BIGINT(20) UNSIGNED NOT NULL');
            }

            $this->addForeignIfMissing('usuario_permisos', 'usuario_permisos_idusuario_foreign', 'idusuario', 'users', 'id', 'CASCADE');
            $this->addUniqueIfMissing('usuario_permisos', 'usuario_permisos_unique', ['idusuario', 'idventana', 'idaccion']);
        }
    }

    public function down(): void
    {
        $this->renameIdUsuarioColumn('audit_logs', 'audit_logs_idusuario_foreign', 'audit_logs_user_id_foreign', true);
        $this->renameIdUsuarioColumn('login_attempts', 'login_attempts_idusuario_foreign', 'login_attempts_user_id_foreign', true);

        if (Schema::hasTable('usuario_permisos')) {
            $this->dropForeignIfExists('usuario_permisos', 'usuario_permisos_idusuario_foreign');
            $this->dropIndexIfExists('usuario_permisos', 'usuario_permisos_unique');

            if (Schema::hasColumn('usuario_permisos', 'idusuario') && ! Schema::hasColumn('usuario_permisos', 'user_id')) {
                DB::statement('ALTER TABLE usuario_permisos CHANGE idusuario user_id BIGINT(20) UNSIGNED NOT NULL');
            }

            $this->addForeignIfMissing('usuario_permisos', 'usuario_permisos_user_id_foreign', 'user_id', 'users', 'id', 'CASCADE');
            $this->addUniqueIfMissing('usuario_permisos', 'usuario_permisos_unique', ['user_id', 'idventana', 'idaccion']);
        }
    }

    private function renameUserColumn(string $table, string $oldForeign, string $newForeign, bool $nullable): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $this->dropForeignIfExists($table, $oldForeign);
        $this->dropIndexIfExists($table, "{$table}_user_id_index");
        $this->dropIndexIfExists($table, "{$table}_user_id_created_at_index");

        if (Schema::hasColumn($table, 'user_id') && ! Schema::hasColumn($table, 'idusuario')) {
            $nullSql = $nullable ? 'NULL' : 'NOT NULL';
            DB::statement("ALTER TABLE {$table} CHANGE user_id idusuario BIGINT(20) UNSIGNED {$nullSql}");
        }

        $this->addIndexIfMissing($table, "{$table}_idusuario_index", ['idusuario']);
        if (Schema::hasColumn($table, 'created_at')) {
            $this->addIndexIfMissing($table, "{$table}_idusuario_created_at_index", ['idusuario', 'created_at']);
        }
        $this->addForeignIfMissing($table, $newForeign, 'idusuario', 'users', 'id', 'SET NULL');
    }

    private function renameIdUsuarioColumn(string $table, string $oldForeign, string $newForeign, bool $nullable): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $this->dropForeignIfExists($table, $oldForeign);
        $this->dropIndexIfExists($table, "{$table}_idusuario_index");
        $this->dropIndexIfExists($table, "{$table}_idusuario_created_at_index");

        if (Schema::hasColumn($table, 'idusuario') && ! Schema::hasColumn($table, 'user_id')) {
            $nullSql = $nullable ? 'NULL' : 'NOT NULL';
            DB::statement("ALTER TABLE {$table} CHANGE idusuario user_id BIGINT(20) UNSIGNED {$nullSql}");
        }

        $this->addIndexIfMissing($table, "{$table}_user_id_index", ['user_id']);
        if (Schema::hasColumn($table, 'created_at')) {
            $this->addIndexIfMissing($table, "{$table}_user_id_created_at_index", ['user_id', 'created_at']);
        }
        $this->addForeignIfMissing($table, $newForeign, 'user_id', 'users', 'id', 'SET NULL');
    }

    private function addForeignIfMissing(
        string $table,
        string $constraint,
        string $column,
        string $referencedTable,
        string $referencedColumn,
        string $onDelete
    ): void {
        $exists = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->exists();

        if (! $exists) {
            DB::statement(
                "ALTER TABLE {$table} ADD CONSTRAINT {$constraint} FOREIGN KEY ({$column}) REFERENCES {$referencedTable}({$referencedColumn}) ON DELETE {$onDelete}"
            );
        }
    }

    private function dropForeignIfExists(string $table, string $constraint): void
    {
        $exists = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();

        if ($exists) {
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$constraint}");
        }
    }

    private function addIndexIfMissing(string $table, string $index, array $columns): void
    {
        if (! $this->indexExists($table, $index)) {
            DB::statement("ALTER TABLE {$table} ADD INDEX {$index} (" . implode(', ', $columns) . ')');
        }
    }

    private function addUniqueIfMissing(string $table, string $index, array $columns): void
    {
        if (! $this->indexExists($table, $index)) {
            DB::statement("ALTER TABLE {$table} ADD UNIQUE {$index} (" . implode(', ', $columns) . ')');
        }
    }

    private function dropIndexIfExists(string $table, string $index): void
    {
        if ($this->indexExists($table, $index)) {
            DB::statement("ALTER TABLE {$table} DROP INDEX {$index}");
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        return DB::table('information_schema.STATISTICS')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->exists();
    }
};
