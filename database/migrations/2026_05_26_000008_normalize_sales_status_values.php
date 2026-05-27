<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->normalizeTable('ventas');
        $this->normalizeTable('nota_credito_venta');
        $this->normalizeColumn('cobros', 'cobro_estado');
    }

    public function down(): void
    {
        $this->abbreviateTable('ventas');
        $this->abbreviateTable('nota_credito_venta');
        $this->abbreviateColumn('cobros', 'cobro_estado');
    }

    private function normalizeTable(string $table): void
    {
        $this->normalizeColumn($table, 'estado');
    }

    private function abbreviateTable(string $table): void
    {
        $this->abbreviateColumn($table, 'estado');
    }

    private function normalizeColumn(string $table, string $column): void
    {
        DB::table($table)->where($column, 'R')->update([$column => 'Realizado']);
        DB::table($table)->where($column, 'P')->update([$column => 'Pendiente']);
        DB::table($table)->where($column, 'A')->update([$column => 'Anulado']);
        DB::table($table)->where($column, 'F')->update([$column => 'Finalizado']);
        DB::table($table)->where($column, 'PC')->update([$column => 'Pendiente de Cobro']);
        DB::table($table)->where($column, 'C')->update([$column => 'Cancelado']);
    }

    private function abbreviateColumn(string $table, string $column): void
    {
        DB::table($table)->where($column, 'Realizado')->update([$column => 'R']);
        DB::table($table)->where($column, 'Pendiente')->update([$column => 'P']);
        DB::table($table)->where($column, 'Anulado')->update([$column => 'A']);
        DB::table($table)->where($column, 'Finalizado')->update([$column => 'F']);
        DB::table($table)->where($column, 'Pendiente de Cobro')->update([$column => 'PC']);
        DB::table($table)->where($column, 'Cancelado')->update([$column => 'C']);
    }
};
