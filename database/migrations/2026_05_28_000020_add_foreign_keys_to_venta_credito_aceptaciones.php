<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('venta_credito_aceptaciones')) {
            return;
        }

        Schema::table('venta_credito_aceptaciones', function (Blueprint $table): void {
            $table->foreign('idventa', 'vca_venta_fk')
                ->references('idventa')
                ->on('ventas')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('idcliente', 'vca_cliente_fk')
                ->references('idcliente')
                ->on('clientes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('venta_credito_aceptaciones')) {
            return;
        }

        Schema::table('venta_credito_aceptaciones', function (Blueprint $table): void {
            $table->dropForeign('vca_venta_fk');
            $table->dropForeign('vca_cliente_fk');
        });
    }
};
