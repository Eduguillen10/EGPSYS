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
            if (! Schema::hasColumn('nota_remision_venta', 'nro_remision')) {
                $table->string('nro_remision', 20)->nullable()->after('idnota_remision_venta')->index();
            }

            if (! Schema::hasColumn('nota_remision_venta', 'timbrado')) {
                $table->string('timbrado', 30)->nullable()->after('idvehiculo');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'fecha_inicio_traslado')) {
                $table->date('fecha_inicio_traslado')->nullable()->after('fecha_traslado');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'fecha_fin_traslado')) {
                $table->date('fecha_fin_traslado')->nullable()->after('fecha_inicio_traslado');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'motivo_traslado')) {
                $table->string('motivo_traslado', 80)->default('Venta')->after('fecha_fin_traslado');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'ciudad_partida')) {
                $table->string('ciudad_partida', 100)->nullable()->after('punto_partida');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'departamento_partida')) {
                $table->string('departamento_partida', 100)->nullable()->after('ciudad_partida');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'ciudad_llegada')) {
                $table->string('ciudad_llegada', 100)->nullable()->after('punto_llegada');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'departamento_llegada')) {
                $table->string('departamento_llegada', 100)->nullable()->after('ciudad_llegada');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'transportista_nombre')) {
                $table->string('transportista_nombre', 150)->nullable()->after('departamento_llegada');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'transportista_documento')) {
                $table->string('transportista_documento', 50)->nullable()->after('transportista_nombre');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'transportista_direccion')) {
                $table->string('transportista_direccion', 180)->nullable()->after('transportista_documento');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'chofer_nombre')) {
                $table->string('chofer_nombre', 150)->nullable()->after('transportista_direccion');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'chofer_documento')) {
                $table->string('chofer_documento', 50)->nullable()->after('chofer_nombre');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'chofer_direccion')) {
                $table->string('chofer_direccion', 180)->nullable()->after('chofer_documento');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'vehiculo_chapa')) {
                $table->string('vehiculo_chapa', 30)->nullable()->after('chofer_direccion');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'vehiculo_modelo')) {
                $table->string('vehiculo_modelo', 80)->nullable()->after('vehiculo_chapa');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'vehiculo_chasis')) {
                $table->string('vehiculo_chasis', 80)->nullable()->after('vehiculo_modelo');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'comprobante_tipo')) {
                $table->string('comprobante_tipo', 40)->nullable()->after('vehiculo_chasis');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'comprobante_numero')) {
                $table->string('comprobante_numero', 30)->nullable()->after('comprobante_tipo');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'comprobante_fecha')) {
                $table->date('comprobante_fecha')->nullable()->after('comprobante_numero');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'comprobante_timbrado')) {
                $table->string('comprobante_timbrado', 30)->nullable()->after('comprobante_fecha');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'motivo_anulacion')) {
                $table->string('motivo_anulacion', 255)->nullable()->after('observacion');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'fecha_anulacion')) {
                $table->timestamp('fecha_anulacion')->nullable()->after('motivo_anulacion');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'hash_documento')) {
                $table->string('hash_documento', 64)->nullable()->after('fecha_anulacion');
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
                'nro_remision',
                'timbrado',
                'fecha_inicio_traslado',
                'fecha_fin_traslado',
                'motivo_traslado',
                'ciudad_partida',
                'departamento_partida',
                'ciudad_llegada',
                'departamento_llegada',
                'transportista_nombre',
                'transportista_documento',
                'transportista_direccion',
                'chofer_nombre',
                'chofer_documento',
                'chofer_direccion',
                'vehiculo_chapa',
                'vehiculo_modelo',
                'vehiculo_chasis',
                'comprobante_tipo',
                'comprobante_numero',
                'comprobante_fecha',
                'comprobante_timbrado',
                'motivo_anulacion',
                'fecha_anulacion',
                'hash_documento',
            ] as $column) {
                if (Schema::hasColumn('nota_remision_venta', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
