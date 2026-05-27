<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $modulo = DB::table('modulos')->where('nombre', 'Referenciales')->first();

        if (! $modulo) {
            return;
        }

        $ventanaId = DB::table('ventanas')->where('permiso_clave', 'estados_referenciales')->value('idventana');

        if (! $ventanaId) {
            $orden = ((int) DB::table('ventanas')->where('idmodulo', $modulo->idmodulo)->max('orden')) + 1;

            $ventanaId = DB::table('ventanas')->insertGetId([
                'idmodulo' => $modulo->idmodulo,
                'nombre' => 'Estados Referenciales',
                'ruta' => '/referenciales/estados',
                'permiso_clave' => 'estados_referenciales',
                'orden' => $orden,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $acciones = DB::table('acciones')->pluck('idaccion');
        $usuarios = DB::table('users')->pluck('id');
        $ahora = now();

        foreach ($usuarios as $userId) {
            foreach ($acciones as $accionId) {
                DB::table('usuario_permisos')->updateOrInsert(
                    [
                        'user_id' => $userId,
                        'idventana' => $ventanaId,
                        'idaccion' => $accionId,
                    ],
                    [
                        'created_at' => $ahora,
                        'updated_at' => $ahora,
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        $ventanaId = DB::table('ventanas')->where('permiso_clave', 'estados_referenciales')->value('idventana');

        if (! $ventanaId) {
            return;
        }

        DB::table('usuario_permisos')->where('idventana', $ventanaId)->delete();
        DB::table('ventanas')->where('idventana', $ventanaId)->delete();
    }
};
