<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('det_formacobro', function (Blueprint $table) {
            $table->bigInteger('monto_recibido')->nullable()->after('monto_detformacobro');
            $table->bigInteger('vuelto')->nullable()->after('monto_recibido');
        });
    }

    public function down(): void
    {
        Schema::table('det_formacobro', function (Blueprint $table) {
            $table->dropColumn(['monto_recibido', 'vuelto']);
        });
    }
};
