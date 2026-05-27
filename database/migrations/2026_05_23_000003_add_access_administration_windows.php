<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();
        $idmodulo = DB::table('modulos')->where('nombre', 'Acceso')->value('idmodulo');

        if (!$idmodulo) {
            $idmodulo = DB::table('modulos')->insertGetId([
                'nombre' => 'Acceso',
                'icono' => 'fa fa-user',
                'orden' => 5,
                'estado' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $ventanas = [
            ['nombre' => 'Modulos', 'ruta' => '/acceso/modulos', 'permiso_clave' => 'modulos', 'orden' => 3],
            ['nombre' => 'Ventanas', 'ruta' => '/acceso/ventanas', 'permiso_clave' => 'ventanas', 'orden' => 4],
            ['nombre' => 'Acciones', 'ruta' => '/acceso/acciones', 'permiso_clave' => 'acciones', 'orden' => 5],
        ];

        foreach ($ventanas as $ventana) {
            if (DB::table('ventanas')->where('permiso_clave', $ventana['permiso_clave'])->exists()) {
                continue;
            }

            $idventana = DB::table('ventanas')->insertGetId($ventana + [
                'idmodulo' => $idmodulo,
                'estado' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $this->asignarPermisos($idventana, $now);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $claves = ['modulos', 'ventanas', 'acciones'];
        $idventanas = DB::table('ventanas')->whereIn('permiso_clave', $claves)->pluck('idventana');

        DB::table('usuario_permisos')->whereIn('idventana', $idventanas)->delete();
        DB::table('ventanas')->whereIn('permiso_clave', $claves)->delete();
    }

    private function asignarPermisos(int $idventana, $now): void
    {
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
