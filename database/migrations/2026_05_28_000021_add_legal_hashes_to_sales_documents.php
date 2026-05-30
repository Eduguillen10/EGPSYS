<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'ventas',
            'cobros',
            'nota_credito_venta',
            'nota_debito_venta',
            'nota_remision_venta',
            'venta_credito_aceptaciones',
        ] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (! Schema::hasColumn($tableName, 'hash_documento')) {
                    $table->string('hash_documento', 64)->nullable()->index();
                }

                if (! Schema::hasColumn($tableName, 'hash_anulacion')) {
                    $table->string('hash_anulacion', 64)->nullable();
                }

                if (! Schema::hasColumn($tableName, 'hash_version')) {
                    $table->string('hash_version', 30)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'ventas',
            'cobros',
            'nota_credito_venta',
            'nota_debito_venta',
            'nota_remision_venta',
            'venta_credito_aceptaciones',
        ] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                foreach (['hash_documento', 'hash_anulacion', 'hash_version'] as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
