<?php

use App\Services\LegalDocumentHashService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cobros') || ! Schema::hasColumn('cobros', 'hash_documento')) {
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

    public function down(): void
    {
        // No se revierte: el hash anterior no puede reconstruirse sin descartar la numeracion actual del recibo.
    }
};
