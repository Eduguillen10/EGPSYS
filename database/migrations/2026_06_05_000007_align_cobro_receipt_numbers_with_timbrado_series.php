<?php

use App\Services\LegalDocumentHashService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cobros') || ! Schema::hasColumn('cobros', 'nro_recibo')) {
            return;
        }

        DB::table('cobros')
            ->whereNotNull('nro_recibo')
            ->orderBy('id_cobro')
            ->select('id_cobro', 'idsucursal')
            ->get()
            ->each(function ($cobro): void {
                DB::table('cobros')
                    ->where('id_cobro', $cobro->id_cobro)
                    ->update([
                        'nro_recibo' => $this->generarNumeroRecibo((int) $cobro->idsucursal, (int) $cobro->id_cobro),
                    ]);
            });

        $this->rehashCobros();
    }

    public function down(): void
    {
        if (! Schema::hasTable('cobros') || ! Schema::hasColumn('cobros', 'nro_recibo')) {
            return;
        }

        DB::table('cobros')
            ->whereNotNull('nro_recibo')
            ->orderBy('id_cobro')
            ->select('id_cobro', 'idsucursal')
            ->get()
            ->each(function ($cobro): void {
                DB::table('cobros')
                    ->where('id_cobro', $cobro->id_cobro)
                    ->update([
                        'nro_recibo' => str_pad((string) $cobro->idsucursal, 3, '0', STR_PAD_LEFT)
                            . '-'
                            . str_pad((string) $cobro->id_cobro, 7, '0', STR_PAD_LEFT),
                    ]);
            });

        $this->rehashCobros();
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

    private function rehashCobros(): void
    {
        if (! Schema::hasColumn('cobros', 'hash_documento')) {
            return;
        }

        $hashService = app(LegalDocumentHashService::class);

        DB::table('cobros')
            ->whereNotNull('hash_documento')
            ->orderBy('id_cobro')
            ->pluck('id_cobro')
            ->each(function ($idCobro) use ($hashService): void {
                DB::table('cobros')
                    ->where('id_cobro', $idCobro)
                    ->update([
                        'hash_documento' => $hashService->hashCobro((int) $idCobro),
                        'hash_version' => LegalDocumentHashService::VERSION,
                    ]);
            });
    }
};
