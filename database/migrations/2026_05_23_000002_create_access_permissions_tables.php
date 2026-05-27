<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('modulos', function (Blueprint $table) {
            $table->id('idmodulo');
            $table->string('nombre');
            $table->string('icono')->default('fa fa-folder');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::create('ventanas', function (Blueprint $table) {
            $table->id('idventana');
            $table->foreignId('idmodulo')->constrained('modulos', 'idmodulo')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('ruta');
            $table->string('permiso_clave')->unique();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::create('acciones', function (Blueprint $table) {
            $table->id('idaccion');
            $table->string('nombre');
            $table->string('clave')->unique();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });

        Schema::create('usuario_permisos', function (Blueprint $table) {
            $table->id('idusuariopermiso');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('idventana')->constrained('ventanas', 'idventana')->cascadeOnDelete();
            $table->foreignId('idaccion')->constrained('acciones', 'idaccion')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'idventana', 'idaccion'], 'usuario_permisos_unique');
        });

        $now = now();

        $acciones = [
            ['nombre' => 'Ver', 'clave' => 'ver', 'orden' => 1],
            ['nombre' => 'Crear', 'clave' => 'crear', 'orden' => 2],
            ['nombre' => 'Editar', 'clave' => 'editar', 'orden' => 3],
            ['nombre' => 'Anular/Eliminar', 'clave' => 'anular', 'orden' => 4],
        ];

        foreach ($acciones as $accion) {
            DB::table('acciones')->insert($accion + ['created_at' => $now, 'updated_at' => $now]);
        }

        $modulos = [
            [
                'nombre' => 'Productos',
                'icono' => 'fa fa-laptop',
                'orden' => 1,
                'ventanas' => [
                    ['nombre' => 'Productos', 'ruta' => '/referenciales/productos', 'permiso_clave' => 'productos', 'orden' => 1],
                    ['nombre' => 'Stock', 'ruta' => '/referenciales/stock', 'permiso_clave' => 'stock', 'orden' => 2],
                ],
            ],
            [
                'nombre' => 'Compras',
                'icono' => 'fa fa-shopping-cart',
                'orden' => 2,
                'ventanas' => [
                    ['nombre' => 'Pedidos de Compra', 'ruta' => '/compras/pedido', 'permiso_clave' => 'pedido', 'orden' => 1],
                    ['nombre' => 'Presupuestos de Compras', 'ruta' => '/compras/presupuesto', 'permiso_clave' => 'presupuesto', 'orden' => 2],
                    ['nombre' => 'Orden de Compras', 'ruta' => '/compras/orden', 'permiso_clave' => 'orden', 'orden' => 3],
                    ['nombre' => 'Compras', 'ruta' => '/compras/compra', 'permiso_clave' => 'compra', 'orden' => 4],
                    ['nombre' => 'Ajustes', 'ruta' => '/compras/ajuste', 'permiso_clave' => 'ajuste', 'orden' => 5],
                ],
            ],
            [
                'nombre' => 'Ventas',
                'icono' => 'fa fa-money',
                'orden' => 3,
                'ventanas' => [
                    ['nombre' => 'Apertura y Cierre', 'ruta' => '/ventas/apertura', 'permiso_clave' => 'apertura', 'orden' => 1],
                    ['nombre' => 'Ventas', 'ruta' => '/ventas/venta', 'permiso_clave' => 'venta', 'orden' => 2],
                    ['nombre' => 'Cuentas a Cobrar', 'ruta' => '/ventas/cuenta_cobrar', 'permiso_clave' => 'cuenta_cobrar', 'orden' => 3],
                    ['nombre' => 'Cobros', 'ruta' => '/ventas/cobro', 'permiso_clave' => 'cobro', 'orden' => 4],
                    ['nombre' => 'Libro Ventas', 'ruta' => '/ventas/libro_ventas', 'permiso_clave' => 'libro_ventas', 'orden' => 5],
                    ['nombre' => 'Nota de Credito', 'ruta' => '/ventas/nota_creditov', 'permiso_clave' => 'nota_creditov', 'orden' => 6],
                ],
            ],
            [
                'nombre' => 'Referenciales',
                'icono' => 'fa fa-list',
                'orden' => 4,
                'ventanas' => [
                    ['nombre' => 'Marcas', 'ruta' => '/referenciales/marcas', 'permiso_clave' => 'marcas', 'orden' => 1],
                    ['nombre' => 'Rubros', 'ruta' => '/referenciales/rubros', 'permiso_clave' => 'rubros', 'orden' => 2],
                    ['nombre' => 'Bancos', 'ruta' => '/referenciales/bancos', 'permiso_clave' => 'bancos', 'orden' => 3],
                    ['nombre' => 'Ciudades', 'ruta' => '/referenciales/ciudades', 'permiso_clave' => 'ciudades', 'orden' => 4],
                    ['nombre' => 'Nacionalidades', 'ruta' => '/referenciales/nacionalidades', 'permiso_clave' => 'nacionalidades', 'orden' => 5],
                    ['nombre' => 'Clientes', 'ruta' => '/referenciales/clientes', 'permiso_clave' => 'clientes', 'orden' => 6],
                    ['nombre' => 'Proveedores', 'ruta' => '/referenciales/proveedores', 'permiso_clave' => 'proveedores', 'orden' => 7],
                    ['nombre' => 'Tipo Impuesto', 'ruta' => '/referenciales/tipo_impuesto', 'permiso_clave' => 'tipo_impuesto', 'orden' => 8],
                    ['nombre' => 'Cargos', 'ruta' => '/referenciales/cargos', 'permiso_clave' => 'cargos', 'orden' => 9],
                    ['nombre' => 'Empleados', 'ruta' => '/referenciales/empleados', 'permiso_clave' => 'empleados', 'orden' => 10],
                    ['nombre' => 'Tipo Documento', 'ruta' => '/referenciales/tipos_documentos', 'permiso_clave' => 'tipos_documentos', 'orden' => 11],
                    ['nombre' => 'Tipo Cliente', 'ruta' => '/referenciales/tipos_clientes', 'permiso_clave' => 'tipos_clientes', 'orden' => 12],
                    ['nombre' => 'Depositos', 'ruta' => '/referenciales/depositos', 'permiso_clave' => 'depositos', 'orden' => 13],
                    ['nombre' => 'Cajas', 'ruta' => '/referenciales/cajas', 'permiso_clave' => 'cajas', 'orden' => 14],
                    ['nombre' => 'Tipo Arqueo', 'ruta' => '/referenciales/tipo_arqueo', 'permiso_clave' => 'tipo_arqueo', 'orden' => 15],
                    ['nombre' => 'Vehiculos', 'ruta' => '/referenciales/vehiculos', 'permiso_clave' => 'vehiculos', 'orden' => 16],
                    ['nombre' => 'Choferes', 'ruta' => '/referenciales/choferes', 'permiso_clave' => 'choferes', 'orden' => 17],
                    ['nombre' => 'Forma Cobro', 'ruta' => '/referenciales/formacobro', 'permiso_clave' => 'formacobro', 'orden' => 18],
                    ['nombre' => 'Tarjetas', 'ruta' => '/referenciales/tarjetas', 'permiso_clave' => 'tarjetas', 'orden' => 19],
                    ['nombre' => 'Entidad Emisora', 'ruta' => '/referenciales/entidademisora', 'permiso_clave' => 'entidademisora', 'orden' => 20],
                    ['nombre' => 'Motivos', 'ruta' => '/referenciales/motivo', 'permiso_clave' => 'motivo', 'orden' => 21],
                    ['nombre' => 'Empresas', 'ruta' => '/referenciales/empresas', 'permiso_clave' => 'empresas', 'orden' => 22],
                    ['nombre' => 'Sucursales', 'ruta' => '/referenciales/sucursales', 'permiso_clave' => 'sucursales', 'orden' => 23],
                    ['nombre' => 'Timbrado', 'ruta' => '/referenciales/timbrado', 'permiso_clave' => 'timbrado', 'orden' => 24],
                ],
            ],
            [
                'nombre' => 'Acceso',
                'icono' => 'fa fa-user',
                'orden' => 5,
                'ventanas' => [
                    ['nombre' => 'Usuarios', 'ruta' => '/acceso/usuario', 'permiso_clave' => 'usuarios', 'orden' => 1],
                    ['nombre' => 'Intentos de Acceso', 'ruta' => '/acceso/intentos', 'permiso_clave' => 'intentos_acceso', 'orden' => 2],
                ],
            ],
        ];

        foreach ($modulos as $modulo) {
            $ventanas = $modulo['ventanas'];
            unset($modulo['ventanas']);

            $idmodulo = DB::table('modulos')->insertGetId($modulo + [
                'estado' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($ventanas as $ventana) {
                DB::table('ventanas')->insert($ventana + [
                    'idmodulo' => $idmodulo,
                    'estado' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $userIds = DB::table('users')->pluck('id');
        $ventanaIds = DB::table('ventanas')->pluck('idventana');
        $accionIds = DB::table('acciones')->pluck('idaccion');
        $permisos = [];

        foreach ($userIds as $userId) {
            foreach ($ventanaIds as $idventana) {
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
        }

        foreach (array_chunk($permisos, 500) as $chunk) {
            DB::table('usuario_permisos')->insert($chunk);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario_permisos');
        Schema::dropIfExists('acciones');
        Schema::dropIfExists('ventanas');
        Schema::dropIfExists('modulos');
    }
};
