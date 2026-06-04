<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tipo_ajuste')) {
            DB::statement("
                CREATE TABLE tipo_ajuste (
                    idtipo_ajuste INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    descripcion VARCHAR(50) NOT NULL,
                    estado VARCHAR(20) NOT NULL DEFAULT 'Activo',
                    PRIMARY KEY (idtipo_ajuste),
                    UNIQUE KEY uq_tipo_ajuste_descripcion (descripcion),
                    KEY idx_tipo_ajuste_estado (estado)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }

        if (! DB::table('tipo_ajuste')->where('descripcion', 'Entrada')->exists()) {
            DB::table('tipo_ajuste')->insert(['descripcion' => 'Entrada', 'estado' => 'Activo']);
        }

        if (! DB::table('tipo_ajuste')->where('descripcion', 'Salida')->exists()) {
            DB::table('tipo_ajuste')->insert(['descripcion' => 'Salida', 'estado' => 'Activo']);
        }

        if (! Schema::hasTable('ajuste')) {
            DB::statement("
                CREATE TABLE ajuste (
                    idajuste INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    idsucursal INT(10) UNSIGNED NOT NULL,
                    iddeposito INT(10) UNSIGNED NOT NULL,
                    idtipo_ajuste INT(10) UNSIGNED NOT NULL,
                    idmotivo INT(10) UNSIGNED NOT NULL,
                    idusuario BIGINT(20) UNSIGNED NOT NULL,
                    usuario VARCHAR(100) NOT NULL,
                    fecha DATE NOT NULL,
                    observacion VARCHAR(255) NULL,
                    estado VARCHAR(20) NOT NULL DEFAULT 'Realizado',
                    PRIMARY KEY (idajuste),
                    KEY idx_ajuste_sucursal (idsucursal),
                    KEY idx_ajuste_deposito (iddeposito),
                    KEY idx_ajuste_tipo (idtipo_ajuste),
                    KEY idx_ajuste_motivo (idmotivo),
                    KEY idx_ajuste_usuario (idusuario),
                    KEY idx_ajuste_fecha (fecha),
                    CONSTRAINT fk_ajuste_sucursal FOREIGN KEY (idsucursal) REFERENCES sucursales(idsucursal),
                    CONSTRAINT fk_ajuste_deposito FOREIGN KEY (iddeposito) REFERENCES depositos(iddeposito),
                    CONSTRAINT fk_ajuste_tipo FOREIGN KEY (idtipo_ajuste) REFERENCES tipo_ajuste(idtipo_ajuste),
                    CONSTRAINT fk_ajuste_motivo FOREIGN KEY (idmotivo) REFERENCES motivo(idmotivo),
                    CONSTRAINT fk_ajuste_usuario FOREIGN KEY (idusuario) REFERENCES users(id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }

        if (! Schema::hasTable('ajuste_detalle')) {
            DB::statement("
                CREATE TABLE ajuste_detalle (
                    idajuste_detalle INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    idajuste INT(10) UNSIGNED NOT NULL,
                    idproducto INT(10) UNSIGNED NOT NULL,
                    items INT(11) NOT NULL,
                    cantidad DECIMAL(12,3) NOT NULL,
                    PRIMARY KEY (idajuste_detalle),
                    KEY idx_ajuste_detalle_ajuste (idajuste),
                    KEY idx_ajuste_detalle_producto (idproducto),
                    CONSTRAINT fk_ajuste_detalle_ajuste FOREIGN KEY (idajuste) REFERENCES ajuste(idajuste),
                    CONSTRAINT fk_ajuste_detalle_producto FOREIGN KEY (idproducto) REFERENCES productos(idproducto)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }

        $this->agregarVentanaTipoAjuste();
    }

    public function down(): void
    {
        $idventanas = DB::table('ventanas')->where('permiso_clave', 'tipo_ajuste')->pluck('idventana');
        DB::table('usuario_permisos')->whereIn('idventana', $idventanas)->delete();
        DB::table('ventanas')->where('permiso_clave', 'tipo_ajuste')->delete();

        Schema::dropIfExists('ajuste_detalle');
        Schema::dropIfExists('ajuste');
        Schema::dropIfExists('tipo_ajuste');
    }

    private function agregarVentanaTipoAjuste(): void
    {
        if (! Schema::hasTable('ventanas') || DB::table('ventanas')->where('permiso_clave', 'tipo_ajuste')->exists()) {
            return;
        }

        $now = now();
        $idmodulo = DB::table('modulos')->where('nombre', 'Referenciales')->value('idmodulo');

        if (! $idmodulo) {
            $idmodulo = DB::table('modulos')->insertGetId([
                'nombre' => 'Referenciales',
                'icono' => 'fa fa-list',
                'orden' => 4,
                'estado' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $idventana = DB::table('ventanas')->insertGetId([
            'idmodulo' => $idmodulo,
            'nombre' => 'Tipo Ajuste',
            'ruta' => '/referenciales/tipo_ajuste',
            'permiso_clave' => 'tipo_ajuste',
            'orden' => 25,
            'estado' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $userIds = DB::table('users')->pluck('id');
        $accionIds = DB::table('acciones')->pluck('idaccion');
        $permisos = [];

        foreach ($userIds as $userId) {
            foreach ($accionIds as $idaccion) {
                $permisos[] = [
                    'user_id' => $userId,
                    'idventana' => $idventana,
                    'idaccion' => $idaccion,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($permisos, 500) as $chunk) {
            DB::table('usuario_permisos')->insert($chunk);
        }
    }
};
