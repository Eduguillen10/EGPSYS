<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('nota_debito_compra')) {
            Schema::create('nota_debito_compra', function (Blueprint $table): void {
                $table->increments('idnota_debitoc');
                $table->unsignedInteger('idcompra')->index();
                $table->unsignedInteger('idsucursal')->index();
                $table->unsignedInteger('iddeposito')->index();
                $table->unsignedInteger('idproveedor')->index();
                $table->unsignedBigInteger('idusuario')->index();
                $table->string('ruc', 20);
                $table->string('nro_nota_debito', 20)->index();
                $table->string('timbrado', 20);
                $table->date('fecha_registro');
                $table->date('fecha_factura');
                $table->date('fecha_vencimiento')->nullable();
                $table->string('concepto', 100);
                $table->unsignedInteger('montoiva10')->nullable();
                $table->unsignedInteger('montoiva5')->nullable();
                $table->unsignedInteger('montogravada10')->nullable();
                $table->unsignedInteger('montogravada5')->nullable();
                $table->unsignedInteger('montoexenta')->nullable();
                $table->unsignedInteger('montonota_debito_compra')->nullable();
                $table->boolean('mueve_stock')->default(true);
                $table->string('estado', 20)->default('Realizado');

                $table->foreign('idcompra', 'fk_nd_compra_compra')->references('idcompra')->on('compras');
                $table->foreign('idsucursal', 'fk_nd_compra_sucursal')->references('idsucursal')->on('sucursales');
                $table->foreign('iddeposito', 'fk_nd_compra_deposito')->references('iddeposito')->on('depositos');
                $table->foreign('idproveedor', 'fk_nd_compra_proveedor')->references('idproveedor')->on('proveedores');
                $table->foreign('idusuario', 'fk_nd_compra_usuario')->references('id')->on('users');
            });
        }

        if (! Schema::hasTable('nota_debito_compra_detalle')) {
            Schema::create('nota_debito_compra_detalle', function (Blueprint $table): void {
                $table->increments('idnota_debitoc_detalle');
                $table->unsignedInteger('idnota_debitoc')->index();
                $table->unsignedInteger('idproducto')->index();
                $table->unsignedInteger('items');
                $table->unsignedInteger('cantidad');
                $table->unsignedInteger('precio_compra');
                $table->unsignedInteger('iva10')->nullable();
                $table->unsignedInteger('iva5')->nullable();
                $table->unsignedInteger('gravada10')->nullable();
                $table->unsignedInteger('gravada5')->nullable();
                $table->unsignedInteger('exenta')->nullable();
                $table->unsignedInteger('montoitems')->nullable();

                $table->foreign('idnota_debitoc', 'fk_nd_compra_detalle_cabecera')->references('idnota_debitoc')->on('nota_debito_compra');
                $table->foreign('idproducto', 'fk_nd_compra_detalle_producto')->references('idproducto')->on('productos');
            });
        }
    }

    public function down(): void
    {
        $this->dropForeignIfExists('nota_debito_compra_detalle', 'fk_nd_compra_detalle_producto');
        $this->dropForeignIfExists('nota_debito_compra_detalle', 'fk_nd_compra_detalle_cabecera');
        $this->dropForeignIfExists('nota_debito_compra', 'fk_nd_compra_usuario');
        $this->dropForeignIfExists('nota_debito_compra', 'fk_nd_compra_proveedor');
        $this->dropForeignIfExists('nota_debito_compra', 'fk_nd_compra_deposito');
        $this->dropForeignIfExists('nota_debito_compra', 'fk_nd_compra_sucursal');
        $this->dropForeignIfExists('nota_debito_compra', 'fk_nd_compra_compra');

        Schema::dropIfExists('nota_debito_compra_detalle');
        Schema::dropIfExists('nota_debito_compra');
    }

    private function dropForeignIfExists(string $table, string $constraint): void
    {
        $exists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->exists();

        if ($exists) {
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$constraint}");
        }
    }
};
