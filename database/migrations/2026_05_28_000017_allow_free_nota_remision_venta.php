<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('nota_remision_venta')) {
            return;
        }

        DB::statement('ALTER TABLE nota_remision_venta MODIFY idventa INT UNSIGNED NULL');
        DB::statement('ALTER TABLE nota_remision_venta MODIFY idcliente INT UNSIGNED NULL');

        Schema::table('nota_remision_venta', function (Blueprint $table): void {
            if (! Schema::hasColumn('nota_remision_venta', 'tipo_origen')) {
                $table->string('tipo_origen', 30)->default('Venta')->after('idcliente');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'destinatario_nombre')) {
                $table->string('destinatario_nombre', 150)->nullable()->after('tipo_origen');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'destinatario_documento')) {
                $table->string('destinatario_documento', 50)->nullable()->after('destinatario_nombre');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'destinatario_direccion')) {
                $table->string('destinatario_direccion', 180)->nullable()->after('destinatario_documento');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'idsucursal_origen')) {
                $table->unsignedInteger('idsucursal_origen')->nullable()->index()->after('idvehiculo');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'iddeposito_origen')) {
                $table->unsignedInteger('iddeposito_origen')->nullable()->index()->after('idsucursal_origen');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'idsucursal_destino')) {
                $table->unsignedInteger('idsucursal_destino')->nullable()->index()->after('iddeposito_origen');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'iddeposito_destino')) {
                $table->unsignedInteger('iddeposito_destino')->nullable()->index()->after('idsucursal_destino');
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
                'tipo_origen',
                'destinatario_nombre',
                'destinatario_documento',
                'destinatario_direccion',
                'idsucursal_origen',
                'iddeposito_origen',
                'idsucursal_destino',
                'iddeposito_destino',
            ] as $column) {
                if (Schema::hasColumn('nota_remision_venta', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
