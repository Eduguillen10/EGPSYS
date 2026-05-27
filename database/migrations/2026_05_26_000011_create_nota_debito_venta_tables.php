<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('nota_debito_venta')) {
            Schema::create('nota_debito_venta', function (Blueprint $table): void {
                $table->integer('idnota_debitov', true);
                $table->unsignedInteger('idventa')->index();
                $table->unsignedInteger('idsucursal')->index();
                $table->unsignedInteger('idcliente')->index();
                $table->unsignedInteger('iddeposito')->index();
                $table->string('num_documento', 15);
                $table->string('nro_factura', 50);
                $table->string('condicion', 50)->nullable();
                $table->date('fecha_registro')->nullable();
                $table->string('concepto', 100)->nullable();
                $table->integer('totaliva10')->nullable();
                $table->integer('totaliva5')->nullable();
                $table->integer('totalgravada10')->nullable();
                $table->integer('totalgravada5')->nullable();
                $table->integer('totalexenta')->nullable();
                $table->integer('totalventa')->nullable();
                $table->string('estado', 50)->nullable();
                $table->string('timbrado', 10)->nullable();
                $table->string('usuario', 100)->nullable();
                $table->date('fecha_factura')->nullable();
                $table->date('fecha_vencimiento')->nullable();
            });
        }

        if (! Schema::hasTable('nota_debito_venta_detalle')) {
            Schema::create('nota_debito_venta_detalle', function (Blueprint $table): void {
                $table->integer('idnota_debitov_detalle', true);
                $table->integer('idnota_debitov')->index();
                $table->unsignedInteger('idproducto')->index();
                $table->integer('items');
                $table->integer('cantidad');
                $table->integer('precio_venta');
                $table->integer('iva10')->nullable();
                $table->integer('iva5')->nullable();
                $table->integer('gravada10')->nullable();
                $table->integer('gravada5')->nullable();
                $table->integer('exenta')->nullable();
                $table->integer('totalitems')->nullable();
            });
        }

        $this->registrarVentanaPermisos();
    }

    public function down(): void
    {
        $ventanaId = DB::table('ventanas')->where('permiso_clave', 'nota_debitov')->value('idventana');

        if ($ventanaId) {
            DB::table('usuario_permisos')->where('idventana', $ventanaId)->delete();
            DB::table('ventanas')->where('idventana', $ventanaId)->delete();
        }

        Schema::dropIfExists('nota_debito_venta_detalle');
        Schema::dropIfExists('nota_debito_venta');
    }

    private function registrarVentanaPermisos(): void
    {
        $modulo = DB::table('modulos')->where('nombre', 'Ventas')->first();

        if (! $modulo) {
            return;
        }

        $now = now();
        $ventanaId = DB::table('ventanas')->where('permiso_clave', 'nota_debitov')->value('idventana');

        if (! $ventanaId) {
            $ventanaId = DB::table('ventanas')->insertGetId([
                'idmodulo' => $modulo->idmodulo,
                'nombre' => 'Nota de Debito',
                'ruta' => '/ventas/nota_debitov',
                'permiso_clave' => 'nota_debitov',
                'orden' => ((int) DB::table('ventanas')->where('idmodulo', $modulo->idmodulo)->max('orden')) + 1,
                'estado' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $creditoVentanaId = DB::table('ventanas')->where('permiso_clave', 'nota_creditov')->value('idventana');
        $permisosBase = collect();

        if ($creditoVentanaId) {
            $permisosBase = DB::table('usuario_permisos')
                ->where('idventana', $creditoVentanaId)
                ->select('user_id', 'idaccion')
                ->distinct()
                ->get();
        }

        if ($permisosBase->isEmpty()) {
            $usuarios = DB::table('users')->pluck('id');
            $acciones = DB::table('acciones')->pluck('idaccion');

            foreach ($usuarios as $userId) {
                foreach ($acciones as $accionId) {
                    $permisosBase->push((object) [
                        'user_id' => $userId,
                        'idaccion' => $accionId,
                    ]);
                }
            }
        }

        foreach ($permisosBase as $permiso) {
            DB::table('usuario_permisos')->updateOrInsert(
                [
                    'user_id' => $permiso->user_id,
                    'idventana' => $ventanaId,
                    'idaccion' => $permiso->idaccion,
                ],
                [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
};
