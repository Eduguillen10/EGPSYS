<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('nota_credito_compra')) {
            return;
        }

        if (! Schema::hasColumn('nota_credito_compra', 'timbrado')) {
            DB::statement('ALTER TABLE nota_credito_compra ADD timbrado VARCHAR(20) NULL AFTER estado');
        }

        if (Schema::hasColumn('nota_credito_compra', 'condicion')) {
            DB::statement('ALTER TABLE nota_credito_compra DROP COLUMN condicion');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('nota_credito_compra')) {
            return;
        }

        if (! Schema::hasColumn('nota_credito_compra', 'condicion')) {
            DB::statement('ALTER TABLE nota_credito_compra ADD condicion VARCHAR(50) NULL AFTER nro_factura');
        }
    }
};
