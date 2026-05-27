<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;

class NotaDebitovDetalle extends Model
{
    use HasAuditTrail;

    protected $table = 'nota_debito_venta_detalle';

    protected $primaryKey = 'idnota_debitov_detalle';

    public $timestamps = false;

    protected $fillable = [
        'idnota_debitov',
        'items',
        'idproducto',
        'cantidad',
        'precio_venta',
        'iva10',
        'iva5',
        'gravada10',
        'gravada5',
        'exenta',
        'totalitems',
    ];

    public function notaDebito()
    {
        return $this->belongsTo(NotaDebitoV::class, 'idnota_debitov', 'idnota_debitov');
    }

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'idproducto', 'idproducto');
    }
}
