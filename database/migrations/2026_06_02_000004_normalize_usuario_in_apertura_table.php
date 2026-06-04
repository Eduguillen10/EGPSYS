<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('apertura')) {
            return;
        }

        $this->dropForeignIfExists('apertura', 'apertura_users_FK');
        $this->dropForeignIfExists('apertura', 'fk_apertura_usuario');

        if (Schema::hasColumn('apertura', 'id') && ! Schema::hasColumn('apertura', 'idusuario')) {
            DB::statement('ALTER TABLE apertura CHANGE id idusuario BIGINT(20) UNSIGNED NOT NULL');
        }

        if (! Schema::hasColumn('apertura', 'idusuario')) {
            Schema::table('apertura', function (Blueprint $table) {
                $table->unsignedBigInteger('idusuario')->nullable()->after('idsucursal');
            });

            if (Schema::hasColumn('apertura', 'usuario')) {
                DB::statement('
                    UPDATE apertura a
                    LEFT JOIN users u ON u.name = a.usuario
                    SET a.idusuario = u.id
                    WHERE a.idusuario IS NULL
                ');
            }

            $fallbackUserId = (int) (DB::table('users')->orderBy('id')->value('id') ?? 1);
            DB::table('apertura')->whereNull('idusuario')->update(['idusuario' => $fallbackUserId]);
            DB::statement('ALTER TABLE apertura MODIFY idusuario BIGINT(20) UNSIGNED NOT NULL');
        }

        $this->addForeignIfMissing('apertura', 'fk_apertura_usuario', 'idusuario', 'users', 'id');

        if (Schema::hasColumn('apertura', 'usuario')) {
            Schema::table('apertura', function (Blueprint $table) {
                $table->dropColumn('usuario');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('apertura')) {
            return;
        }

        if (! Schema::hasColumn('apertura', 'usuario')) {
            Schema::table('apertura', function (Blueprint $table) {
                $table->string('usuario', 100)->nullable()->after('idusuario');
            });
        }

        if (Schema::hasColumn('apertura', 'idusuario')) {
            DB::statement('
                UPDATE apertura a
                LEFT JOIN users u ON u.id = a.idusuario
                SET a.usuario = u.name
                WHERE a.usuario IS NULL
            ');

            $this->dropForeignIfExists('apertura', 'fk_apertura_usuario');

            if (! Schema::hasColumn('apertura', 'id')) {
                DB::statement('ALTER TABLE apertura CHANGE idusuario id BIGINT(20) UNSIGNED NOT NULL');
            }

            $this->addForeignIfMissing('apertura', 'apertura_users_FK', 'id', 'users', 'id');
        }
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
