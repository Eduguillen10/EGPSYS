<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->addForeignKey(
            'nota_debito_venta',
            'nota_debito_venta_ventas_fk',
            'idventa',
            'ventas',
            'idventa'
        );

        $this->addForeignKey(
            'nota_debito_venta',
            'nota_debito_venta_sucursales_fk',
            'idsucursal',
            'sucursales',
            'idsucursal'
        );

        $this->addForeignKey(
            'nota_debito_venta',
            'nota_debito_venta_clientes_fk',
            'idcliente',
            'clientes',
            'idcliente'
        );

        $this->addForeignKey(
            'nota_debito_venta',
            'nota_debito_venta_depositos_fk',
            'iddeposito',
            'depositos',
            'iddeposito'
        );

        $this->addForeignKey(
            'nota_debito_venta_detalle',
            'nota_debito_venta_detalle_cabecera_fk',
            'idnota_debitov',
            'nota_debito_venta',
            'idnota_debitov',
            'CASCADE'
        );

        $this->addForeignKey(
            'nota_debito_venta_detalle',
            'nota_debito_venta_detalle_productos_fk',
            'idproducto',
            'productos',
            'idproducto'
        );
    }

    public function down(): void
    {
        $this->dropForeignKey('nota_debito_venta_detalle', 'nota_debito_venta_detalle_productos_fk');
        $this->dropForeignKey('nota_debito_venta_detalle', 'nota_debito_venta_detalle_cabecera_fk');
        $this->dropForeignKey('nota_debito_venta', 'nota_debito_venta_depositos_fk');
        $this->dropForeignKey('nota_debito_venta', 'nota_debito_venta_clientes_fk');
        $this->dropForeignKey('nota_debito_venta', 'nota_debito_venta_sucursales_fk');
        $this->dropForeignKey('nota_debito_venta', 'nota_debito_venta_ventas_fk');
    }

    private function addForeignKey(
        string $table,
        string $constraint,
        string $column,
        string $referencedTable,
        string $referencedColumn,
        string $onDelete = 'RESTRICT'
    ): void {
        if ($this->foreignKeyExists($table, $constraint)) {
            return;
        }

        DB::statement("
            ALTER TABLE {$table}
            ADD CONSTRAINT {$constraint}
            FOREIGN KEY ({$column})
            REFERENCES {$referencedTable} ({$referencedColumn})
            ON UPDATE CASCADE
            ON DELETE {$onDelete}
        ");
    }

    private function dropForeignKey(string $table, string $constraint): void
    {
        if (! $this->foreignKeyExists($table, $constraint)) {
            return;
        }

        DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$constraint}");
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();
    }
};
