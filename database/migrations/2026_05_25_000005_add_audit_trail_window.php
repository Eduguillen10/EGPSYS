<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $idmodulo = DB::table('modulos')->where('nombre', 'Acceso')->value('idmodulo');

        if (! $idmodulo) {
            $idmodulo = DB::table('modulos')->insertGetId([
                'nombre' => 'Acceso',
                'icono' => 'fa fa-user',
                'orden' => 5,
                'estado' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (DB::table('ventanas')->where('permiso_clave', 'audit_trail')->exists()) {
            return;
        }

        $idventana = DB::table('ventanas')->insertGetId([
            'idmodulo' => $idmodulo,
            'nombre' => 'Audit Trail',
            'ruta' => '/acceso/auditoria',
            'permiso_clave' => 'audit_trail',
            'orden' => 6,
            'estado' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $accionIds = DB::table('acciones')->pluck('idaccion');
        $userIds = DB::table('users')->pluck('id');
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

    public function down(): void
    {
        $idventanas = DB::table('ventanas')->where('permiso_clave', 'audit_trail')->pluck('idventana');

        DB::table('usuario_permisos')->whereIn('idventana', $idventanas)->delete();
        DB::table('ventanas')->whereIn('idventana', $idventanas)->delete();
    }
};
