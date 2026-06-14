<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addUniqueIfMissing(
            'nota_credito_venta',
            'uq_ncv_timbrado_numero',
            ['timbrado', 'nro_nota_credito']
        );

        $this->addUniqueIfMissing(
            'nota_debito_venta',
            'uq_ndv_timbrado_numero',
            ['timbrado', 'nro_nota_debito']
        );

        $this->addUniqueIfMissing(
            'nota_remision_venta',
            'uq_nrv_timbrado_numero',
            ['idtimbrado', 'nro_remision']
        );
    }

    public function down(): void
    {
        $this->dropIndexIfExists('nota_remision_venta', 'uq_nrv_timbrado_numero');
        $this->dropIndexIfExists('nota_debito_venta', 'uq_ndv_timbrado_numero');
        $this->dropIndexIfExists('nota_credito_venta', 'uq_ncv_timbrado_numero');
    }

    private function addUniqueIfMissing(string $table, string $index, array $columns): void
    {
        if (! Schema::hasTable($table) || $this->indexExists($table, $index)) {
            return;
        }

        foreach ($columns as $column) {
            if (! Schema::hasColumn($table, $column)) {
                return;
            }
        }

        $this->failIfDuplicatesExist($table, $columns);

        $columnList = implode(', ', $columns);
        DB::statement("ALTER TABLE {$table} ADD UNIQUE {$index} ({$columnList})");
    }

    private function failIfDuplicatesExist(string $table, array $columns): void
    {
        $query = DB::table($table)
            ->selectRaw(implode(', ', $columns) . ', COUNT(*) AS cantidad');

        foreach ($columns as $column) {
            $query->whereNotNull($column);
        }

        $duplicate = $query
            ->groupBy($columns)
            ->havingRaw('COUNT(*) > 1')
            ->first();

        if ($duplicate) {
            throw new RuntimeException(
                "No se puede crear el indice unico {$table}: existen numeros duplicados."
            );
        }
    }

    private function dropIndexIfExists(string $table, string $index): void
    {
        if (! $this->indexExists($table, $index)) {
            return;
        }

        DB::statement("ALTER TABLE {$table} DROP INDEX {$index}");
    }

    private function indexExists(string $table, string $index): bool
    {
        if (! Schema::hasTable($table)) {
            return false;
        }

        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->exists();
    }
};
