<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;

class NotaDebitoV extends Model
{
    use HasAuditTrail;

    protected $table = 'nota_debito_venta';

    protected $primaryKey = 'idnota_debitov';

    public $timestamps = false;

    protected $fillable = [
        'idventa',
        'idsucursal',
        'iddeposito',
        'idcliente',
        'num_documento',
        'nro_factura',
        'condicion',
        'fecha_registro',
        'fecha_factura',
        'fecha_vencimiento',
        'concepto',
        'estado',
        'usuario',
        'totaliva10',
        'totaliva5',
        'totalgravada10',
        'totalgravada5',
        'totalexenta',
        'totalventa',
        'timbrado',
    ];

    public function venta()
    {
        return $this->belongsTo(Ventas::class, 'idventa', 'idventa');
    }

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'idcliente', 'idcliente');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursales::class, 'idsucursal', 'idsucursal');
    }

    public function deposito()
    {
        return $this->belongsTo(Depositos::class, 'iddeposito', 'iddeposito');
    }

    public function detalles()
    {
        return $this->hasMany(NotaDebitovDetalle::class, 'idnota_debitov', 'idnota_debitov');
    }
}
