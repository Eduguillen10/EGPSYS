<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('venta_credito_aceptaciones')) {
            Schema::create('venta_credito_aceptaciones', function (Blueprint $table): void {
                $table->increments('idaceptacion_credito');
                $table->unsignedInteger('idventa')->index();
                $table->unsignedInteger('idcliente')->index();
                $table->string('metodo_aceptacion', 50)->default('Firma fisica');
                $table->string('recibido_por', 150);
                $table->string('documento_receptor', 50);
                $table->string('telefono_receptor', 50)->nullable();
                $table->string('relacion_receptor', 80)->nullable();
                $table->integer('monto');
                $table->string('condicion', 100);
                $table->date('fecha_vencimiento')->nullable();
                $table->text('texto_aceptado');
                $table->string('archivo_respaldo', 255)->nullable();
                $table->string('archivo_nombre_original', 255)->nullable();
                $table->string('hash_documento', 64);
                $table->string('ip', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('estado', 30)->default('Aceptado');
                $table->string('usuario', 100);
                $table->timestamps();

                $table->index(['idventa', 'estado'], 'venta_credito_aceptaciones_venta_estado_idx');
            });
        }

        if (! Schema::hasTable('nota_remision_venta')) {
            Schema::create('nota_remision_venta', function (Blueprint $table): void {
                $table->increments('idnota_remision_venta');
                $table->unsignedInteger('idventa')->index();
                $table->unsignedInteger('idcliente')->index();
                $table->unsignedInteger('idchofer')->nullable()->index();
                $table->unsignedInteger('idvehiculo')->nullable()->index();
                $table->date('fecha_emision');
                $table->date('fecha_traslado')->nullable();
                $table->string('punto_partida', 180)->nullable();
                $table->string('punto_llegada', 180)->nullable();
                $table->string('recibido_por', 150)->nullable();
                $table->string('documento_receptor', 50)->nullable();
                $table->string('estado', 30)->default('Emitido');
                $table->string('usuario', 100);
                $table->string('observacion', 255)->nullable();
                $table->timestamps();

                $table->index(['idventa', 'estado'], 'nota_remision_venta_venta_estado_idx');
            });
        }

        if (! Schema::hasTable('nota_remision_venta_detalle')) {
            Schema::create('nota_remision_venta_detalle', function (Blueprint $table): void {
                $table->increments('idnota_remision_venta_detalle');
                $table->unsignedInteger('idnota_remision_venta')->index();
                $table->unsignedInteger('idproducto')->index();
                $table->integer('items');
                $table->decimal('cantidad', 12, 3);
                $table->string('descripcion', 250);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_remision_venta_detalle');
        Schema::dropIfExists('nota_remision_venta');
        Schema::dropIfExists('venta_credito_aceptaciones');
    }
};
