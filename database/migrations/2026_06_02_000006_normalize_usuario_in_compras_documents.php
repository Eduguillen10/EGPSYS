<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $fallbackUserId = (int) (DB::table('users')->orderBy('id')->value('id') ?? 1);

        $this->normalizeNotaCreditoCompra($fallbackUserId);
        $this->normalizeNotaRemisionCompra($fallbackUserId);
    }

    public function down(): void
    {
        if (Schema::hasTable('nota_remision_compra') && Schema::hasColumn('nota_remision_compra', 'idusuario')) {
            if (! Schema::hasColumn('nota_remision_compra', 'usuario')) {
                Schema::table('nota_remision_compra', function (Blueprint $table) {
                    $table->string('usuario', 100)->nullable()->after('idusuario');
                });
            }

            DB::statement('
                UPDATE nota_remision_compra nr
                LEFT JOIN users u ON u.id = nr.idusuario
                SET nr.usuario = u.name
                WHERE nr.usuario IS NULL
            ');

            $this->dropForeignIfExists('nota_remision_compra', 'fk_nrc_usuario');

            if (! Schema::hasColumn('nota_remision_compra', 'user_id')) {
                DB::statement('ALTER TABLE nota_remision_compra CHANGE idusuario user_id BIGINT(20) UNSIGNED NOT NULL');
            }

            $this->addForeignIfMissing('nota_remision_compra', 'fk_nrc_user', 'user_id', 'users', 'id');
        }

        if (Schema::hasTable('nota_credito_compra') && Schema::hasColumn('nota_credito_compra', 'idusuario')) {
            if (! Schema::hasColumn('nota_credito_compra', 'usuario')) {
                Schema::table('nota_credito_compra', function (Blueprint $table) {
                    $table->string('usuario', 100)->nullable()->after('idusuario');
                });
            }

            DB::statement('
                UPDATE nota_credito_compra nc
                LEFT JOIN users u ON u.id = nc.idusuario
                SET nc.usuario = u.name
                WHERE nc.usuario IS NULL
            ');

            $this->dropForeignIfExists('nota_credito_compra', 'fk_nota_credito_compra_usuario');

            Schema::table('nota_credito_compra', function (Blueprint $table) {
                $table->dropColumn('idusuario');
            });
        }
    }

    private function normalizeNotaCreditoCompra(int $fallbackUserId): void
    {
        if (! Schema::hasTable('nota_credito_compra')) {
            return;
        }

        if (! Schema::hasColumn('nota_credito_compra', 'idusuario')) {
            Schema::table('nota_credito_compra', function (Blueprint $table) {
                $table->unsignedBigInteger('idusuario')->nullable();
            });
        }

        if (Schema::hasColumn('nota_credito_compra', 'usuario')) {
            DB::statement('
                UPDATE nota_credito_compra nc
                LEFT JOIN users u ON u.name = nc.usuario
                SET nc.idusuario = COALESCE(u.id, nc.idusuario, ?)
                WHERE nc.idusuario IS NULL
            ', [$fallbackUserId]);
        }

        DB::table('nota_credito_compra')->whereNull('idusuario')->update(['idusuario' => $fallbackUserId]);
        DB::statement('ALTER TABLE nota_credito_compra MODIFY idusuario BIGINT(20) UNSIGNED NOT NULL');

        $this->addForeignIfMissing('nota_credito_compra', 'fk_nota_credito_compra_usuario', 'idusuario', 'users', 'id');

        if (Schema::hasColumn('nota_credito_compra', 'usuario')) {
            Schema::table('nota_credito_compra', function (Blueprint $table) {
                $table->dropColumn('usuario');
            });
        }
    }

    private function normalizeNotaRemisionCompra(int $fallbackUserId): void
    {
        if (! Schema::hasTable('nota_remision_compra')) {
            return;
        }

        $this->dropForeignIfExists('nota_remision_compra', 'fk_nrc_user');
        $this->dropForeignIfExists('nota_remision_compra', 'fk_nrc_usuario');

        if (Schema::hasColumn('nota_remision_compra', 'user_id') && ! Schema::hasColumn('nota_remision_compra', 'idusuario')) {
            DB::statement('ALTER TABLE nota_remision_compra CHANGE user_id idusuario BIGINT(20) UNSIGNED NOT NULL');
        }

        if (! Schema::hasColumn('nota_remision_compra', 'idusuario')) {
            Schema::table('nota_remision_compra', function (Blueprint $table) {
                $table->unsignedBigInteger('idusuario')->nullable();
            });
        }

        if (Schema::hasColumn('nota_remision_compra', 'usuario')) {
            DB::statement('
                UPDATE nota_remision_compra nr
                LEFT JOIN users u ON u.name = nr.usuario
                SET nr.idusuario = COALESCE(nr.idusuario, u.id, ?)
                WHERE nr.idusuario IS NULL
            ', [$fallbackUserId]);
        }

        DB::table('nota_remision_compra')->whereNull('idusuario')->update(['idusuario' => $fallbackUserId]);
        DB::statement('ALTER TABLE nota_remision_compra MODIFY idusuario BIGINT(20) UNSIGNED NOT NULL');

        $this->addForeignIfMissing('nota_remision_compra', 'fk_nrc_usuario', 'idusuario', 'users', 'id');

        if (Schema::hasColumn('nota_remision_compra', 'usuario')) {
            Schema::table('nota_remision_compra', function (Blueprint $table) {
                $table->dropColumn('usuario');
            });
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
