<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;

class NotaDebitocDetalle extends Model
{
    use HasAuditTrail;

    protected $table = 'nota_debito_compra_detalle';

    protected $primaryKey = 'idnota_debitoc_detalle';

    public $timestamps = false;

    protected $fillable = [
        'idnota_debitoc',
        'items',
        'idproducto',
        'cantidad',
        'precio_compra',
        'iva10',
        'iva5',
        'gravada10',
        'gravada5',
        'exenta',
        'montoitems',
    ];
}
