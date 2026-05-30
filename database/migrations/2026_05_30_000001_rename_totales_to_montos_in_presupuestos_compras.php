<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('presupuestos_compras', 'totaliva10')) {
            DB::statement('ALTER TABLE presupuestos_compras CHANGE totaliva10 montoiva10 INT(11) NULL');
        }

        if (Schema::hasColumn('presupuestos_compras', 'totaliva5')) {
            DB::statement('ALTER TABLE presupuestos_compras CHANGE totaliva5 montoiva5 INT(11) NULL');
        }

        if (Schema::hasColumn('presupuestos_compras', 'totalgravada10')) {
            DB::statement('ALTER TABLE presupuestos_compras CHANGE totalgravada10 montogravada10 INT(11) NULL');
        }

        if (Schema::hasColumn('presupuestos_compras', 'totalgravada5')) {
            DB::statement('ALTER TABLE presupuestos_compras CHANGE totalgravada5 montogravada5 INT(11) NULL');
        }

        if (Schema::hasColumn('presupuestos_compras', 'totalexenta')) {
            DB::statement('ALTER TABLE presupuestos_compras CHANGE totalexenta montoexenta INT(11) NULL');
        }

        if (Schema::hasColumn('presupuestos_compras', 'totalpresupuesto_compra')) {
            DB::statement('ALTER TABLE presupuestos_compras CHANGE totalpresupuesto_compra montopresupuesto_compra INT(11) NULL');
        }

        if (Schema::hasColumn('presupuestos_compras_detalle', 'totalitems')) {
            DB::statement('ALTER TABLE presupuestos_compras_detalle CHANGE totalitems montoitems INT(11) NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('presupuestos_compras', 'montoiva10')) {
            DB::statement('ALTER TABLE presupuestos_compras CHANGE montoiva10 totaliva10 INT(11) NULL');
        }

        if (Schema::hasColumn('presupuestos_compras', 'montoiva5')) {
            DB::statement('ALTER TABLE presupuestos_compras CHANGE montoiva5 totaliva5 INT(11) NULL');
        }

        if (Schema::hasColumn('presupuestos_compras', 'montogravada10')) {
            DB::statement('ALTER TABLE presupuestos_compras CHANGE montogravada10 totalgravada10 INT(11) NULL');
        }

        if (Schema::hasColumn('presupuestos_compras', 'montogravada5')) {
            DB::statement('ALTER TABLE presupuestos_compras CHANGE montogravada5 totalgravada5 INT(11) NULL');
        }

        if (Schema::hasColumn('presupuestos_compras', 'montoexenta')) {
            DB::statement('ALTER TABLE presupuestos_compras CHANGE montoexenta totalexenta INT(11) NULL');
        }

        if (Schema::hasColumn('presupuestos_compras', 'montopresupuesto_compra')) {
            DB::statement('ALTER TABLE presupuestos_compras CHANGE montopresupuesto_compra totalpresupuesto_compra INT(11) NULL');
        }

        if (Schema::hasColumn('presupuestos_compras_detalle', 'montoitems')) {
            DB::statement('ALTER TABLE presupuestos_compras_detalle CHANGE montoitems totalitems INT(11) NULL');
        }
    }
};
