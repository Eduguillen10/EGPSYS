<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('orden_compras', 'totaliva10')) {
            DB::statement('ALTER TABLE orden_compras CHANGE totaliva10 montoiva10 INT(11) NULL');
        }

        if (Schema::hasColumn('orden_compras', 'totaliva5')) {
            DB::statement('ALTER TABLE orden_compras CHANGE totaliva5 montoiva5 INT(11) NULL');
        }

        if (Schema::hasColumn('orden_compras', 'totalgravada10')) {
            DB::statement('ALTER TABLE orden_compras CHANGE totalgravada10 montogravada10 INT(11) NULL');
        }

        if (Schema::hasColumn('orden_compras', 'totalgravada5')) {
            DB::statement('ALTER TABLE orden_compras CHANGE totalgravada5 montogravada5 INT(11) NULL');
        }

        if (Schema::hasColumn('orden_compras', 'totalexenta')) {
            DB::statement('ALTER TABLE orden_compras CHANGE totalexenta montoexenta INT(11) NULL');
        }

        if (Schema::hasColumn('orden_compras', 'total_orden_compra')) {
            DB::statement('ALTER TABLE orden_compras CHANGE total_orden_compra monto_orden_compra INT(11) NULL');
        }

        if (Schema::hasColumn('orden_detalle', 'totalitems')) {
            DB::statement('ALTER TABLE orden_detalle CHANGE totalitems montoitems INT(11) NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orden_compras', 'montoiva10')) {
            DB::statement('ALTER TABLE orden_compras CHANGE montoiva10 totaliva10 INT(11) NULL');
        }

        if (Schema::hasColumn('orden_compras', 'montoiva5')) {
            DB::statement('ALTER TABLE orden_compras CHANGE montoiva5 totaliva5 INT(11) NULL');
        }

        if (Schema::hasColumn('orden_compras', 'montogravada10')) {
            DB::statement('ALTER TABLE orden_compras CHANGE montogravada10 totalgravada10 INT(11) NULL');
        }

        if (Schema::hasColumn('orden_compras', 'montogravada5')) {
            DB::statement('ALTER TABLE orden_compras CHANGE montogravada5 totalgravada5 INT(11) NULL');
        }

        if (Schema::hasColumn('orden_compras', 'montoexenta')) {
            DB::statement('ALTER TABLE orden_compras CHANGE montoexenta totalexenta INT(11) NULL');
        }

        if (Schema::hasColumn('orden_compras', 'monto_orden_compra')) {
            DB::statement('ALTER TABLE orden_compras CHANGE monto_orden_compra total_orden_compra INT(11) NULL');
        }

        if (Schema::hasColumn('orden_detalle', 'montoitems')) {
            DB::statement('ALTER TABLE orden_detalle CHANGE montoitems totalitems INT(11) NULL');
        }
    }
};
