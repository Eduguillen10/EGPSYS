<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nota_credito_venta', function (Blueprint $table) {
            if (! Schema::hasColumn('nota_credito_venta', 'nro_nota_credito')) {
                $table->string('nro_nota_credito', 20)->nullable()->after('idnota_creditov')->index();
            }
        });

        Schema::table('nota_debito_venta', function (Blueprint $table) {
            if (! Schema::hasColumn('nota_debito_venta', 'nro_nota_debito')) {
                $table->string('nro_nota_debito', 20)->nullable()->after('idnota_debitov')->index();
            }
        });

        $this->backfill('nota_credito_venta', 'idnota_creditov', 'nro_nota_credito');
        $this->backfill('nota_debito_venta', 'idnota_debitov', 'nro_nota_debito');
    }

    public function down(): void
    {
        Schema::table('nota_credito_venta', function (Blueprint $table) {
            if (Schema::hasColumn('nota_credito_venta', 'nro_nota_credito')) {
                $table->dropColumn('nro_nota_credito');
            }
        });

        Schema::table('nota_debito_venta', function (Blueprint $table) {
            if (Schema::hasColumn('nota_debito_venta', 'nro_nota_debito')) {
                $table->dropColumn('nro_nota_debito');
            }
        });
    }

    private function backfill(string $table, string $pk, string $numberColumn): void
    {
        DB::table($table)
            ->orderBy($pk)
            ->get([$pk, 'idsucursal', 'timbrado', $numberColumn])
            ->each(function ($row) use ($table, $pk, $numberColumn) {
                if (! empty($row->{$numberColumn})) {
                    return;
                }

                $serie = DB::table('timbrado')
                    ->where('idsucursal', (int) $row->idsucursal)
                    ->where(function ($q) use ($row) {
                        if (! empty($row->timbrado)) {
                            $q->where('nro_timbrado', $row->timbrado);
                        }
                    })
                    ->orderByDesc('idtimbrado')
                    ->value('nro_serie');

                if (! $serie) {
                    $serie = DB::table('timbrado')
                        ->where('idsucursal', (int) $row->idsucursal)
                        ->orderByDesc('idtimbrado')
                        ->value('nro_serie');
                }

                $serie = $serie ?: '001-001';
                $numero = $serie . '-' . str_pad((string) $row->{$pk}, 7, '0', STR_PAD_LEFT);

                DB::table($table)
                    ->where($pk, $row->{$pk})
                    ->update([$numberColumn => $numero]);
            });
    }
};
