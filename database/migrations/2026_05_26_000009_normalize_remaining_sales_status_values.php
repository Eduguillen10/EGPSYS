<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('ventas')->where('estado', 'F')->update(['estado' => 'Finalizado']);
        DB::table('ventas')->where('estado', 'PC')->update(['estado' => 'Pendiente de Cobro']);
        DB::table('ventas')->where('estado', 'C')->update(['estado' => 'Cancelado']);
    }

    public function down(): void
    {
        DB::table('ventas')->where('estado', 'Finalizado')->update(['estado' => 'F']);
        DB::table('ventas')->where('estado', 'Pendiente de Cobro')->update(['estado' => 'PC']);
        DB::table('ventas')->where('estado', 'Cancelado')->update(['estado' => 'C']);
    }
};
