<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('venta_credito_aceptaciones') && ! Schema::hasColumn('venta_credito_aceptaciones', 'observacion')) {
            Schema::table('venta_credito_aceptaciones', function (Blueprint $table): void {
                $table->string('observacion', 255)->nullable()->after('usuario');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('venta_credito_aceptaciones') && Schema::hasColumn('venta_credito_aceptaciones', 'observacion')) {
            Schema::table('venta_credito_aceptaciones', function (Blueprint $table): void {
                $table->dropColumn('observacion');
            });
        }
    }
};
