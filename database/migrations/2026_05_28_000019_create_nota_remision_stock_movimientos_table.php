<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('nota_remision_stock_movimientos')) {
            return;
        }

        Schema::create('nota_remision_stock_movimientos', function (Blueprint $table): void {
            $table->increments('idnota_remision_stock_movimiento');
            $table->unsignedInteger('idnota_remision_venta')->index();
            $table->unsignedInteger('idproducto')->index();
            $table->unsignedInteger('iddeposito_origen')->index();
            $table->unsignedInteger('iddeposito_destino')->index();
            $table->decimal('cantidad', 12, 3);
            $table->string('tipo_movimiento', 30)->default('TRASLADO');
            $table->string('estado', 20)->default('Aplicado')->index();
            $table->string('usuario', 100)->nullable();
            $table->timestamps();

            $table->foreign('idnota_remision_venta', 'nrsm_remision_fk')
                ->references('idnota_remision_venta')
                ->on('nota_remision_venta')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('idproducto', 'nrsm_producto_fk')
                ->references('idproducto')
                ->on('productos')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('iddeposito_origen', 'nrsm_deposito_origen_fk')
                ->references('iddeposito')
                ->on('depositos')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('iddeposito_destino', 'nrsm_deposito_destino_fk')
                ->references('iddeposito')
                ->on('depositos')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_remision_stock_movimientos');
    }
};
