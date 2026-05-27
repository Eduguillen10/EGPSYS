<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'marcas',
        'rubros',
        'bancos',
        'nacionalidades',
        'ciudades',
        'clientes',
        'proveedores',
        'tipo_impuesto',
        'cargos',
        'tipos_documentos',
        'tipo_cliente',
        'depositos',
        'tipoarqueo',
        'vehiculo',
        'chofer',
        'formacobro',
        'entidademisora',
        'tarjeta',
        'motivo',
        'empresas',
        'sucursales',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasColumn($table, 'estado')) {
                Schema::table($table, function (Blueprint $table): void {
                    $table->string('estado', 20)->default('Activo')->index();
                });
            }
        }

        DB::table('productos')->where('estado', 'Desactivado')->update(['estado' => 'Inactivo']);
        DB::table('productos')->whereNull('estado')->update(['estado' => 'Activo']);
        DB::table('empleados')->whereNull('estado')->update(['estado' => 'Activo']);
        DB::table('cajas')->whereNull('estado')->update(['estado' => 'Activo']);
        DB::table('timbrado')->whereNull('estado')->update(['estado' => 'Activo']);
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $table) {
            if (Schema::hasColumn($table, 'estado')) {
                Schema::table($table, function (Blueprint $table): void {
                    $table->dropColumn('estado');
                });
            }
        }
    }
};
