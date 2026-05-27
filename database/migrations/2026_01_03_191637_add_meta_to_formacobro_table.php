<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('formacobro', function (Blueprint $table) {
            $table->boolean('requiere_banco')->default(false);
            $table->boolean('requiere_documento')->default(false);
            $table->boolean('requiere_vencimiento')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('formacobro', function (Blueprint $table) {
            $table->dropColumn(['requiere_banco','requiere_documento','requiere_vencimiento']);
        });
    }
};
