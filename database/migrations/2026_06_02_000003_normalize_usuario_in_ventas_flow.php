<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'ventas',
        'cobros',
        'nota_credito_venta',
        'nota_debito_venta',
        'nota_remision_venta',
        'venta_credito_aceptaciones',
    ];

    public function up(): void
    {
        $fallbackUserId = DB::table('users')->orderBy('id')->value('id');

        foreach ($this->tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            if (! Schema::hasColumn($tableName, 'idusuario')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->unsignedBigInteger('idusuario')->nullable();
                });
            }

            if (Schema::hasColumn($tableName, 'usuario')) {
                DB::statement("
                    UPDATE {$tableName} t
                    LEFT JOIN users u ON u.name = t.usuario
                    SET t.idusuario = COALESCE(u.id, t.idusuario, ?)
                    WHERE t.idusuario IS NULL
                ", [$fallbackUserId]);
            } elseif ($fallbackUserId) {
                DB::table($tableName)->whereNull('idusuario')->update(['idusuario' => $fallbackUserId]);
            }

            DB::statement("ALTER TABLE {$tableName} MODIFY idusuario BIGINT(20) UNSIGNED NOT NULL");
            $this->addForeignIfMissing($tableName, "fk_{$tableName}_usuario", 'idusuario', 'users', 'id');

            if (Schema::hasColumn($tableName, 'usuario')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->dropColumn('usuario');
                });
            }
        }

        if (Schema::hasTable('nota_remision_venta') && Schema::hasColumn('nota_remision_venta', 'usuario_recepcion')) {
            if (! Schema::hasColumn('nota_remision_venta', 'idusuario_recepcion')) {
                Schema::table('nota_remision_venta', function (Blueprint $table): void {
                    $table->unsignedBigInteger('idusuario_recepcion')->nullable()->after('observacion_entrega');
                });
            }

            DB::statement("
                UPDATE nota_remision_venta nr
                LEFT JOIN users u ON u.name = nr.usuario_recepcion
                SET nr.idusuario_recepcion = COALESCE(u.id, nr.idusuario_recepcion)
                WHERE nr.usuario_recepcion IS NOT NULL
                  AND nr.usuario_recepcion <> ''
                  AND nr.idusuario_recepcion IS NULL
            ");

            $this->addForeignIfMissing('nota_remision_venta', 'fk_nota_remision_venta_usuario_recepcion', 'idusuario_recepcion', 'users', 'id');

            Schema::table('nota_remision_venta', function (Blueprint $table): void {
                $table->dropColumn('usuario_recepcion');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('nota_remision_venta') && Schema::hasColumn('nota_remision_venta', 'idusuario_recepcion')) {
            if (! Schema::hasColumn('nota_remision_venta', 'usuario_recepcion')) {
                Schema::table('nota_remision_venta', function (Blueprint $table): void {
                    $table->string('usuario_recepcion', 100)->nullable()->after('observacion_entrega');
                });
            }

            DB::statement("
                UPDATE nota_remision_venta nr
                LEFT JOIN users u ON u.id = nr.idusuario_recepcion
                SET nr.usuario_recepcion = u.name
            ");

            $this->dropForeignIfExists('nota_remision_venta', 'fk_nota_remision_venta_usuario_recepcion');

            Schema::table('nota_remision_venta', function (Blueprint $table): void {
                $table->dropColumn('idusuario_recepcion');
            });
        }

        foreach (array_reverse($this->tables) as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            if (! Schema::hasColumn($tableName, 'usuario')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->string('usuario', 100)->nullable();
                });

                DB::statement("
                    UPDATE {$tableName} t
                    LEFT JOIN users u ON u.id = t.idusuario
                    SET t.usuario = u.name
                ");
            }

            $this->dropForeignIfExists($tableName, "fk_{$tableName}_usuario");

            if (Schema::hasColumn($tableName, 'idusuario')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->dropColumn('idusuario');
                });
            }
        }
    }

    private function addForeignIfMissing(string $table, string $foreignName, string $column, string $referencedTable, string $referencedColumn): void
    {
        $exists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $foreignName)
            ->exists();

        if (! $exists) {
            DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$foreignName} FOREIGN KEY ({$column}) REFERENCES {$referencedTable}({$referencedColumn})");
        }
    }

    private function dropForeignIfExists(string $table, string $foreignName): void
    {
        $exists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $foreignName)
            ->exists();

        if ($exists) {
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$foreignName}");
        }
    }
};
