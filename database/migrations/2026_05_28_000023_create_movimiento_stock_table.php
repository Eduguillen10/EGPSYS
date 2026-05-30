<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('movimiento_stock')) {
            return;
        }

        Schema::create('movimiento_stock', function (Blueprint $table): void {
            $table->increments('idmovimiento_stock');
            $table->unsignedInteger('idproducto');
            $table->unsignedInteger('idsucursal');
            $table->unsignedInteger('iddeposito');
            $table->dateTime('fecha');
            $table->string('tipo_origen', 30);
            $table->unsignedInteger('id_origen');
            $table->string('detalle_origen', 30)->nullable();
            $table->enum('operacion', ['ENTRADA', 'SALIDA']);
            $table->decimal('cantidad', 12, 3);
            $table->decimal('costo_unitario', 12, 2)->nullable();
            $table->string('observacion', 255)->nullable();
            $table->string('usuario', 100)->nullable();
            $table->string('estado', 20)->default('ACTIVO');

            $table->index('idproducto', 'idx_mov_stock_producto');
            $table->index('idsucursal', 'idx_mov_stock_sucursal');
            $table->index('iddeposito', 'idx_mov_stock_deposito');
            $table->index(['tipo_origen', 'id_origen'], 'idx_mov_stock_origen');

            $table->foreign('idproducto', 'fk_mov_stock_producto')
                ->references('idproducto')
                ->on('productos')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('idsucursal', 'fk_mov_stock_sucursal')
                ->references('idsucursal')
                ->on('sucursales')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('iddeposito', 'fk_mov_stock_deposito')
                ->references('iddeposito')
                ->on('depositos')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimiento_stock');
    }
};
