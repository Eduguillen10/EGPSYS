<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ajuste') && Schema::hasColumn('ajuste', 'usuario')) {
            Schema::table('ajuste', function (Blueprint $table) {
                $table->dropColumn('usuario');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ajuste') && ! Schema::hasColumn('ajuste', 'usuario')) {
            Schema::table('ajuste', function (Blueprint $table) {
                $table->string('usuario', 100)->nullable()->after('idusuario');
            });
        }
    }
};
