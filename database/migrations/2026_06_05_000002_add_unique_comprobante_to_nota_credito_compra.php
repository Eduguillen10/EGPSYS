<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $indexName = 'uq_nc_compra_proveedor_timbrado_numero';

    public function up(): void
    {
        if (! Schema::hasTable('nota_credito_compra')) {
            return;
        }

        if ($this->indexExists()) {
            return;
        }

        DB::statement("
            ALTER TABLE nota_credito_compra
            ADD UNIQUE {$this->indexName} (idproveedor, timbrado, nro_factura)
        ");
    }

    public function down(): void
    {
        if (! Schema::hasTable('nota_credito_compra') || ! $this->indexExists()) {
            return;
        }

        DB::statement("ALTER TABLE nota_credito_compra DROP INDEX {$this->indexName}");
    }

    private function indexExists(): bool
    {
        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'nota_credito_compra')
            ->where('INDEX_NAME', $this->indexName)
            ->exists();
    }
};
