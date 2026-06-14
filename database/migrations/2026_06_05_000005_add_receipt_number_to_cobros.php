<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cobros')) {
            return;
        }

        Schema::table('cobros', function (Blueprint $table): void {
            if (! Schema::hasColumn('cobros', 'nro_recibo')) {
                $table->string('nro_recibo', 20)->nullable()->after('id_cobro');
            }

            if (! Schema::hasColumn('cobros', 'fecha_recibo')) {
                $table->date('fecha_recibo')->nullable()->after('fecha_cobro');
            }
        });

        $this->backfillRecibos();
        $this->addUniqueIfMissing('cobros', 'uq_cobros_sucursal_recibo', ['idsucursal', 'nro_recibo']);
    }

    public function down(): void
    {
        $this->dropIndexIfExists('cobros', 'uq_cobros_sucursal_recibo');

        if (! Schema::hasTable('cobros')) {
            return;
        }

        Schema::table('cobros', function (Blueprint $table): void {
            if (Schema::hasColumn('cobros', 'fecha_recibo')) {
                $table->dropColumn('fecha_recibo');
            }

            if (Schema::hasColumn('cobros', 'nro_recibo')) {
                $table->dropColumn('nro_recibo');
            }
        });
    }

    private function backfillRecibos(): void
    {
        DB::table('cobros as c')
            ->where('c.cobro_estado', 'Realizado')
            ->where('c.monto_cobro', '>', 0)
            ->whereNull('c.nro_recibo')
            ->whereNotExists(function ($sub): void {
                if (! Schema::hasTable('nota_credito_venta_cobro')) {
                    $sub->selectRaw('1')->from('cobros as c2')->whereRaw('1 = 0');
                    return;
                }

                $sub->selectRaw('1')
                    ->from('nota_credito_venta_cobro as ncc')
                    ->whereColumn('ncc.id_cobro', 'c.id_cobro');
            })
            ->whereNotExists(function ($sub): void {
                $sub->selectRaw('1')
                    ->from('det_formacobro as df_nc')
                    ->whereColumn('df_nc.id_cobro', 'c.id_cobro')
                    ->where(function ($q): void {
                        $q->where('df_nc.documento', 'LIKE', 'NC %')
                            ->orWhere('df_nc.documento', 'LIKE', 'AJUSTE NC %')
                            ->orWhere('df_nc.documento', 'LIKE', 'REING NC %')
                            ->orWhere('df_nc.documento', 'LIKE', 'REING AJUSTE NC %')
                            ->orWhere('df_nc.documento', 'LIKE', 'REINGRESO AJUSTE NC %');
                    });
            })
            ->orderBy('c.id_cobro')
            ->select('c.id_cobro', 'c.idsucursal', 'c.fecha_cobro')
            ->get()
            ->each(function ($cobro): void {
                DB::table('cobros')
                    ->where('id_cobro', $cobro->id_cobro)
                    ->update([
                        'nro_recibo' => $this->generarNumeroRecibo((int) $cobro->idsucursal, (int) $cobro->id_cobro),
                        'fecha_recibo' => $cobro->fecha_cobro,
                    ]);
            });
    }

    private function generarNumeroRecibo(int $idsucursal, int $idCobro): string
    {
        return $this->serieRecibo($idsucursal)
            . '-'
            . str_pad((string) $idCobro, 7, '0', STR_PAD_LEFT);
    }

    private function serieRecibo(int $idsucursal): string
    {
        $serie = DB::table('timbrado')
            ->where('idsucursal', $idsucursal)
            ->where('estado', 'Activo')
            ->whereDate('fecha_vencimiento', '>=', now()->toDateString())
            ->orderByDesc('idtimbrado')
            ->value('nro_serie');

        if (! $serie) {
            $serie = DB::table('timbrado')
                ->where('idsucursal', $idsucursal)
                ->orderByDesc('idtimbrado')
                ->value('nro_serie');
        }

        return $serie ?: str_pad((string) $idsucursal, 3, '0', STR_PAD_LEFT) . '-001';
    }

    private function addUniqueIfMissing(string $table, string $index, array $columns): void
    {
        if (! Schema::hasTable($table) || $this->indexExists($table, $index)) {
            return;
        }

        foreach ($columns as $column) {
            if (! Schema::hasColumn($table, $column)) {
                return;
            }
        }

        $this->failIfDuplicatesExist($table, $columns);

        DB::statement("ALTER TABLE {$table} ADD UNIQUE {$index} (" . implode(', ', $columns) . ')');
    }

    private function failIfDuplicatesExist(string $table, array $columns): void
    {
        $query = DB::table($table)
            ->selectRaw(implode(', ', $columns) . ', COUNT(*) AS cantidad');

        foreach ($columns as $column) {
            $query->whereNotNull($column);
        }

        $duplicate = $query
            ->groupBy($columns)
            ->havingRaw('COUNT(*) > 1')
            ->first();

        if ($duplicate) {
            throw new RuntimeException("No se puede crear el indice unico {$table}: existen numeros de recibo duplicados.");
        }
    }

    private function dropIndexIfExists(string $table, string $index): void
    {
        if (! $this->indexExists($table, $index)) {
            return;
        }

        DB::statement("ALTER TABLE {$table} DROP INDEX {$index}");
    }

    private function indexExists(string $table, string $index): bool
    {
        if (! Schema::hasTable($table)) {
            return false;
        }

        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->exists();
    }
};
