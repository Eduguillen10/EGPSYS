<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('transportistas')) {
            Schema::create('transportistas', function (Blueprint $table): void {
                $table->increments('idtransportista');
                $table->string('nombre', 150);
                $table->string('documento', 50)->nullable()->index();
                $table->string('direccion', 180)->nullable();
                $table->string('telefono', 50)->nullable();
                $table->string('email', 100)->nullable();
                $table->string('estado', 20)->default('Activo')->index();
            });
        }

        if (! Schema::hasTable('destinatarios_remision')) {
            Schema::create('destinatarios_remision', function (Blueprint $table): void {
                $table->increments('iddestinatario_remision');
                $table->string('nombre', 150);
                $table->string('documento', 50)->nullable()->index();
                $table->string('direccion', 180)->nullable();
                $table->string('telefono', 50)->nullable();
                $table->string('email', 100)->nullable();
                $table->string('estado', 20)->default('Activo')->index();
            });
        }

        Schema::table('nota_remision_venta', function (Blueprint $table): void {
            if (! Schema::hasColumn('nota_remision_venta', 'idtransportista')) {
                $table->unsignedInteger('idtransportista')->nullable()->index()->after('idvehiculo');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'iddestinatario_remision')) {
                $table->unsignedInteger('iddestinatario_remision')->nullable()->index()->after('idcliente');
            }

            if (! Schema::hasColumn('nota_remision_venta', 'idtimbrado')) {
                $table->unsignedInteger('idtimbrado')->nullable()->index()->after('idvehiculo');
            }
        });

        $this->migrarTransportistas();
        $this->migrarDestinatarios();
        $this->migrarTimbrados();
        $this->limpiarReferenciasInvalidas();

        Schema::table('nota_remision_venta', function (Blueprint $table): void {
            $columns = [
                'destinatario_nombre',
                'destinatario_documento',
                'destinatario_direccion',
                'idsucursal_origen',
                'idsucursal_destino',
                'timbrado',
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
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('nota_remision_venta', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('nota_remision_venta_detalle', function (Blueprint $table): void {
            if (Schema::hasColumn('nota_remision_venta_detalle', 'descripcion')) {
                $table->dropColumn('descripcion');
            }
        });

        Schema::table('nota_remision_venta', function (Blueprint $table): void {
            $table->foreign('idventa', 'nr_venta_fk')
                ->references('idventa')
                ->on('ventas')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('idcliente', 'nr_cliente_fk')
                ->references('idcliente')
                ->on('clientes')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('iddestinatario_remision', 'nr_destinatario_fk')
                ->references('iddestinatario_remision')
                ->on('destinatarios_remision')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('idchofer', 'nr_chofer_fk')
                ->references('idchofer')
                ->on('chofer')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('idvehiculo', 'nr_vehiculo_fk')
                ->references('idvehiculo')
                ->on('vehiculo')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('idtransportista', 'nr_transportista_fk')
                ->references('idtransportista')
                ->on('transportistas')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('idtimbrado', 'nr_timbrado_fk')
                ->references('idtimbrado')
                ->on('timbrado')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('iddeposito_origen', 'nr_deposito_origen_fk')
                ->references('iddeposito')
                ->on('depositos')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('iddeposito_destino', 'nr_deposito_destino_fk')
                ->references('iddeposito')
                ->on('depositos')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        Schema::table('nota_remision_venta_detalle', function (Blueprint $table): void {
            $table->foreign('idnota_remision_venta', 'nrd_remision_fk')
                ->references('idnota_remision_venta')
                ->on('nota_remision_venta')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('idproducto', 'nrd_producto_fk')
                ->references('idproducto')
                ->on('productos')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        $this->registrarVentanasPermisos();
    }

    public function down(): void
    {
        Schema::table('nota_remision_venta_detalle', function (Blueprint $table): void {
            $table->dropForeign('nrd_remision_fk');
            $table->dropForeign('nrd_producto_fk');

            if (! Schema::hasColumn('nota_remision_venta_detalle', 'descripcion')) {
                $table->string('descripcion', 250)->default('');
            }
        });

        Schema::table('nota_remision_venta', function (Blueprint $table): void {
            foreach ([
                'nr_venta_fk',
                'nr_cliente_fk',
                'nr_destinatario_fk',
                'nr_chofer_fk',
                'nr_vehiculo_fk',
                'nr_transportista_fk',
                'nr_timbrado_fk',
                'nr_deposito_origen_fk',
                'nr_deposito_destino_fk',
            ] as $foreignKey) {
                $table->dropForeign($foreignKey);
            }

            if (! Schema::hasColumn('nota_remision_venta', 'destinatario_nombre')) {
                $table->string('destinatario_nombre', 150)->nullable();
            }
            if (! Schema::hasColumn('nota_remision_venta', 'destinatario_documento')) {
                $table->string('destinatario_documento', 50)->nullable();
            }
            if (! Schema::hasColumn('nota_remision_venta', 'destinatario_direccion')) {
                $table->string('destinatario_direccion', 180)->nullable();
            }
            if (! Schema::hasColumn('nota_remision_venta', 'idsucursal_origen')) {
                $table->unsignedInteger('idsucursal_origen')->nullable()->index();
            }
            if (! Schema::hasColumn('nota_remision_venta', 'idsucursal_destino')) {
                $table->unsignedInteger('idsucursal_destino')->nullable()->index();
            }
            if (! Schema::hasColumn('nota_remision_venta', 'timbrado')) {
                $table->string('timbrado', 30)->nullable();
            }
            if (! Schema::hasColumn('nota_remision_venta', 'transportista_nombre')) {
                $table->string('transportista_nombre', 150)->nullable();
            }
            if (! Schema::hasColumn('nota_remision_venta', 'transportista_documento')) {
                $table->string('transportista_documento', 50)->nullable();
            }
            if (! Schema::hasColumn('nota_remision_venta', 'transportista_direccion')) {
                $table->string('transportista_direccion', 180)->nullable();
            }
            if (! Schema::hasColumn('nota_remision_venta', 'chofer_nombre')) {
                $table->string('chofer_nombre', 150)->nullable();
            }
            if (! Schema::hasColumn('nota_remision_venta', 'chofer_documento')) {
                $table->string('chofer_documento', 50)->nullable();
            }
            if (! Schema::hasColumn('nota_remision_venta', 'chofer_direccion')) {
                $table->string('chofer_direccion', 180)->nullable();
            }
            if (! Schema::hasColumn('nota_remision_venta', 'vehiculo_chapa')) {
                $table->string('vehiculo_chapa', 30)->nullable();
            }
            if (! Schema::hasColumn('nota_remision_venta', 'vehiculo_modelo')) {
                $table->string('vehiculo_modelo', 80)->nullable();
            }
            if (! Schema::hasColumn('nota_remision_venta', 'vehiculo_chasis')) {
                $table->string('vehiculo_chasis', 80)->nullable();
            }
            if (! Schema::hasColumn('nota_remision_venta', 'comprobante_tipo')) {
                $table->string('comprobante_tipo', 40)->nullable();
            }
            if (! Schema::hasColumn('nota_remision_venta', 'comprobante_numero')) {
                $table->string('comprobante_numero', 30)->nullable();
            }
            if (! Schema::hasColumn('nota_remision_venta', 'comprobante_fecha')) {
                $table->date('comprobante_fecha')->nullable();
            }
            if (! Schema::hasColumn('nota_remision_venta', 'comprobante_timbrado')) {
                $table->string('comprobante_timbrado', 30)->nullable();
            }

            foreach (['idtransportista', 'iddestinatario_remision', 'idtimbrado'] as $column) {
                if (Schema::hasColumn('nota_remision_venta', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        $idventanas = DB::table('ventanas')
            ->whereIn('permiso_clave', ['transportistas', 'destinatarios_remision'])
            ->pluck('idventana');

        DB::table('usuario_permisos')->whereIn('idventana', $idventanas)->delete();
        DB::table('ventanas')->whereIn('idventana', $idventanas)->delete();

        Schema::dropIfExists('destinatarios_remision');
        Schema::dropIfExists('transportistas');
    }

    private function migrarTransportistas(): void
    {
        if (! Schema::hasColumn('nota_remision_venta', 'transportista_nombre')) {
            return;
        }

        $remisiones = DB::table('nota_remision_venta')
            ->select('idnota_remision_venta', 'transportista_nombre', 'transportista_documento', 'transportista_direccion')
            ->get();

        foreach ($remisiones as $remision) {
            $nombre = trim((string) $remision->transportista_nombre);
            $documento = trim((string) $remision->transportista_documento) ?: null;
            $direccion = trim((string) $remision->transportista_direccion) ?: null;

            if ($nombre === '' && $documento === null && $direccion === null) {
                continue;
            }

            if ($nombre === '') {
                $nombre = 'Transportista ' . $remision->idnota_remision_venta;
            }

            $idtransportista = $this->obtenerTransportistaId($nombre, $documento, $direccion);

            DB::table('nota_remision_venta')
                ->where('idnota_remision_venta', $remision->idnota_remision_venta)
                ->update(['idtransportista' => $idtransportista]);
        }
    }

    private function migrarDestinatarios(): void
    {
        if (! Schema::hasColumn('nota_remision_venta', 'destinatario_nombre')) {
            return;
        }

        $remisiones = DB::table('nota_remision_venta')
            ->select('idnota_remision_venta', 'idcliente', 'destinatario_nombre', 'destinatario_documento', 'destinatario_direccion')
            ->whereNull('idcliente')
            ->get();

        foreach ($remisiones as $remision) {
            $nombre = trim((string) $remision->destinatario_nombre);
            $documento = trim((string) $remision->destinatario_documento) ?: null;
            $direccion = trim((string) $remision->destinatario_direccion) ?: null;

            if ($nombre === '' && $documento === null && $direccion === null) {
                continue;
            }

            if ($nombre === '') {
                $nombre = 'Destinatario ' . $remision->idnota_remision_venta;
            }

            $iddestinatario = $this->obtenerDestinatarioId($nombre, $documento, $direccion);

            DB::table('nota_remision_venta')
                ->where('idnota_remision_venta', $remision->idnota_remision_venta)
                ->update(['iddestinatario_remision' => $iddestinatario]);
        }
    }

    private function migrarTimbrados(): void
    {
        if (! Schema::hasColumn('nota_remision_venta', 'timbrado')) {
            return;
        }

        $remisiones = DB::table('nota_remision_venta')
            ->select('idnota_remision_venta', 'timbrado')
            ->whereNotNull('timbrado')
            ->get();

        foreach ($remisiones as $remision) {
            $idtimbrado = DB::table('timbrado')
                ->where('nro_timbrado', (int) $remision->timbrado)
                ->value('idtimbrado');

            if (! $idtimbrado) {
                continue;
            }

            DB::table('nota_remision_venta')
                ->where('idnota_remision_venta', $remision->idnota_remision_venta)
                ->update(['idtimbrado' => $idtimbrado]);
        }
    }

    private function limpiarReferenciasInvalidas(): void
    {
        $referencias = [
            ['idventa', 'ventas', 'idventa'],
            ['idcliente', 'clientes', 'idcliente'],
            ['iddestinatario_remision', 'destinatarios_remision', 'iddestinatario_remision'],
            ['idchofer', 'chofer', 'idchofer'],
            ['idvehiculo', 'vehiculo', 'idvehiculo'],
            ['idtransportista', 'transportistas', 'idtransportista'],
            ['idtimbrado', 'timbrado', 'idtimbrado'],
            ['iddeposito_origen', 'depositos', 'iddeposito'],
            ['iddeposito_destino', 'depositos', 'iddeposito'],
        ];

        foreach ($referencias as [$column, $table, $id]) {
            DB::statement(
                "UPDATE nota_remision_venta nr
                 LEFT JOIN {$table} ref ON nr.{$column} = ref.{$id}
                 SET nr.{$column} = NULL
                 WHERE nr.{$column} IS NOT NULL AND ref.{$id} IS NULL"
            );
        }

        DB::statement(
            'DELETE nrd FROM nota_remision_venta_detalle nrd
             LEFT JOIN nota_remision_venta nr ON nrd.idnota_remision_venta = nr.idnota_remision_venta
             WHERE nr.idnota_remision_venta IS NULL'
        );

        DB::statement(
            'DELETE nrd FROM nota_remision_venta_detalle nrd
             LEFT JOIN productos p ON nrd.idproducto = p.idproducto
             WHERE p.idproducto IS NULL'
        );
    }

    private function obtenerTransportistaId(string $nombre, ?string $documento, ?string $direccion): int
    {
        $query = DB::table('transportistas');

        if ($documento) {
            $query->where('documento', $documento);
        } else {
            $query->where('nombre', $nombre)->where('direccion', $direccion);
        }

        $id = $query->value('idtransportista');

        if ($id) {
            return (int) $id;
        }

        return DB::table('transportistas')->insertGetId([
            'nombre' => $nombre,
            'documento' => $documento,
            'direccion' => $direccion,
            'estado' => 'Activo',
        ]);
    }

    private function obtenerDestinatarioId(string $nombre, ?string $documento, ?string $direccion): int
    {
        $query = DB::table('destinatarios_remision');

        if ($documento) {
            $query->where('documento', $documento);
        } else {
            $query->where('nombre', $nombre)->where('direccion', $direccion);
        }

        $id = $query->value('iddestinatario_remision');

        if ($id) {
            return (int) $id;
        }

        return DB::table('destinatarios_remision')->insertGetId([
            'nombre' => $nombre,
            'documento' => $documento,
            'direccion' => $direccion,
            'estado' => 'Activo',
        ]);
    }

    private function registrarVentanasPermisos(): void
    {
        $modulo = DB::table('modulos')->where('nombre', 'Referenciales')->first();

        if (! $modulo) {
            return;
        }

        $now = now();
        $ventanas = [
            ['nombre' => 'Transportistas', 'ruta' => '/referenciales/transportistas', 'permiso_clave' => 'transportistas'],
            ['nombre' => 'Destinatarios de Remision', 'ruta' => '/referenciales/destinatarios_remision', 'permiso_clave' => 'destinatarios_remision'],
        ];

        foreach ($ventanas as $ventana) {
            $idventana = DB::table('ventanas')->where('permiso_clave', $ventana['permiso_clave'])->value('idventana');

            if (! $idventana) {
                $orden = ((int) DB::table('ventanas')->where('idmodulo', $modulo->idmodulo)->max('orden')) + 1;

                $idventana = DB::table('ventanas')->insertGetId($ventana + [
                    'idmodulo' => $modulo->idmodulo,
                    'orden' => $orden,
                    'estado' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $accionIds = DB::table('acciones')->pluck('idaccion');
            $userIds = DB::table('users')->pluck('id');

            foreach ($userIds as $userId) {
                foreach ($accionIds as $idaccion) {
                    DB::table('usuario_permisos')->updateOrInsert(
                        [
                            'user_id' => $userId,
                            'idventana' => $idventana,
                            'idaccion' => $idaccion,
                        ],
                        [
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]
                    );
                }
            }
        }
    }
};
