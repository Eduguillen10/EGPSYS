<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('nota_remision_venta')) {
            return;
        }

        Schema::table('nota_remision_venta', function (Blueprint $table): void {
            if (! Schema::hasColumn('nota_remision_venta', 'fecha_entrega')) {
                $table->date('fecha_entrega')->nullable()->after('documento_receptor');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'hora_entrega')) {
                $table->time('hora_entrega')->nullable()->after('fecha_entrega');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'observacion_entrega')) {
                $table->string('observacion_entrega', 255)->nullable()->after('hora_entrega');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'usuario_recepcion')) {
                $table->string('usuario_recepcion', 100)->nullable()->after('observacion_entrega');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'recepcion_registrada_at')) {
                $table->timestamp('recepcion_registrada_at')->nullable()->after('usuario_recepcion');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'hash_recepcion')) {
                $table->string('hash_recepcion', 64)->nullable()->after('hash_documento');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('nota_remision_venta')) {
            return;
        }

        Schema::table('nota_remision_venta', function (Blueprint $table): void {
            foreach ([
                'fecha_entrega',
                'hora_entrega',
                'observacion_entrega',
                'usuario_recepcion',
                'recepcion_registrada_at',
                'hash_recepcion',
            ] as $column) {
                if (Schema::hasColumn('nota_remision_venta', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
