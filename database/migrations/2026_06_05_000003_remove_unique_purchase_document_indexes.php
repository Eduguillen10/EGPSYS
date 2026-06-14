<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexIfMissing('nota_credito_compra', 'idx_nc_compra_proveedor', 'idproveedor');
        $this->addIndexIfMissing('nota_remision_compra', 'idx_nrc_proveedor', 'idproveedor');

        $this->dropIndexIfExists('nota_credito_compra', 'uq_nc_compra_proveedor_timbrado_numero');
        $this->dropIndexIfExists('nota_remision_compra', 'uq_nrc_proveedor_comprobante');
    }

    public function down(): void
    {
        if (Schema::hasTable('nota_credito_compra') && ! $this->indexExists('nota_credito_compra', 'uq_nc_compra_proveedor_timbrado_numero')) {
            DB::statement('ALTER TABLE nota_credito_compra ADD UNIQUE uq_nc_compra_proveedor_timbrado_numero (idproveedor, timbrado, nro_factura)');
        }

        if (Schema::hasTable('nota_remision_compra') && ! $this->indexExists('nota_remision_compra', 'uq_nrc_proveedor_comprobante')) {
            DB::statement('ALTER TABLE nota_remision_compra ADD UNIQUE uq_nrc_proveedor_comprobante (idproveedor, nro_comprobante)');
        }
    }

    private function dropIndexIfExists(string $table, string $index): void
    {
        if (! $this->indexExists($table, $index)) {
            return;
        }

        DB::statement("ALTER TABLE {$table} DROP INDEX {$index}");
    }

    private function addIndexIfMissing(string $table, string $index, string $column): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column) || $this->indexExists($table, $index)) {
            return;
        }

        DB::statement("ALTER TABLE {$table} ADD INDEX {$index} ({$column})");
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
