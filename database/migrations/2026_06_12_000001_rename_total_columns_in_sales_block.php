<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->renameColumns('ventas', [
            'totaliva10' => 'montoiva10',
            'totaliva5' => 'montoiva5',
            'totalgravada10' => 'montogravada10',
            'totalgravada5' => 'montogravada5',
            'totalexenta' => 'montoexenta',
            'totalventa' => 'montoventa',
        ]);

        $this->renameColumns('venta_detalle', [
            'totalitems' => 'montoitems',
        ]);

        $this->renameColumns('libro_venta', [
            'totaliva10' => 'montoiva10',
            'totaliva5' => 'montoiva5',
            'totalgravada10' => 'montogravada10',
            'totalgravada5' => 'montogravada5',
            'totalexenta' => 'montoexenta',
            'totalventa' => 'montoventa',
        ]);
    }

    public function down(): void
    {
        $this->renameColumns('libro_venta', [
            'montoiva10' => 'totaliva10',
            'montoiva5' => 'totaliva5',
            'montogravada10' => 'totalgravada10',
            'montogravada5' => 'totalgravada5',
            'montoexenta' => 'totalexenta',
            'montoventa' => 'totalventa',
        ]);

        $this->renameColumns('venta_detalle', [
            'montoitems' => 'totalitems',
        ]);

        $this->renameColumns('ventas', [
            'montoiva10' => 'totaliva10',
            'montoiva5' => 'totaliva5',
            'montogravada10' => 'totalgravada10',
            'montogravada5' => 'totalgravada5',
            'montoexenta' => 'totalexenta',
            'montoventa' => 'totalventa',
        ]);
    }

    private function renameColumns(string $table, array $columns): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        foreach ($columns as $from => $to) {
            if (Schema::hasColumn($table, $from) && ! Schema::hasColumn($table, $to)) {
                DB::statement("ALTER TABLE {$table} CHANGE {$from} {$to} INT(11) NULL");
            }
        }
    }
};
