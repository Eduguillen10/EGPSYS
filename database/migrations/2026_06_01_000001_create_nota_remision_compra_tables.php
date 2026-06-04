<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nota_remision_compra', function (Blueprint $table) {
            $table->increments('idremisionc');
            $table->unsignedInteger('idordencompra');
            $table->unsignedInteger('idproveedor');
            $table->unsignedInteger('iddeposito');
            $table->unsignedInteger('idsucursal');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('usuario', 100);
            $table->string('nro_comprobante', 30);
            $table->date('fecha')->nullable();
            $table->date('fecha_remision');
            $table->string('motivo_traslado', 100);
            $table->string('chofer', 100)->nullable();
            $table->string('documento_chofer', 30)->nullable();
            $table->string('vehiculo', 100)->nullable();
            $table->string('chapa', 30)->nullable();
            $table->string('observacion', 255)->nullable();
            $table->string('estado', 50)->default('Realizado');

            $table->index(['idordencompra', 'estado'], 'idx_nrc_orden_estado');
            $table->index(['idproveedor', 'nro_comprobante'], 'idx_nrc_proveedor_comprobante');
            $table->unique(['idproveedor', 'nro_comprobante'], 'uq_nrc_proveedor_comprobante');

            $table->foreign('idordencompra', 'fk_nrc_orden')
                ->references('idordencompra')
                ->on('orden_compras');
            $table->foreign('idproveedor', 'fk_nrc_proveedor')
                ->references('idproveedor')
                ->on('proveedores');
            $table->foreign('iddeposito', 'fk_nrc_deposito')
                ->references('iddeposito')
                ->on('depositos');
            $table->foreign('idsucursal', 'fk_nrc_sucursal')
                ->references('idsucursal')
                ->on('sucursales');
            $table->foreign('user_id', 'fk_nrc_user')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });

        Schema::create('nota_remision_compra_detalle', function (Blueprint $table) {
            $table->increments('idremisionc_detalle');
            $table->unsignedInteger('idremisionc');
            $table->integer('idorden_detalle');
            $table->unsignedInteger('idproducto');
            $table->integer('items');
            $table->decimal('cantidad', 12, 3);

            $table->index(['idremisionc', 'idproducto'], 'idx_nrcd_remision_producto');
            $table->index('idorden_detalle', 'idx_nrcd_orden_detalle');

            $table->foreign('idremisionc', 'fk_nrcd_remision')
                ->references('idremisionc')
                ->on('nota_remision_compra')
                ->cascadeOnDelete();
            $table->foreign('idorden_detalle', 'fk_nrcd_orden_detalle')
                ->references('idorden_detalle')
                ->on('orden_detalle');
            $table->foreign('idproducto', 'fk_nrcd_producto')
                ->references('idproducto')
                ->on('productos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_remision_compra_detalle');
        Schema::dropIfExists('nota_remision_compra');
    }
};
