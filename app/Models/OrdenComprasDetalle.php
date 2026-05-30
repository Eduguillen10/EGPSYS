<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenComprasDetalle extends Model
{
    use HasAuditTrail;

    protected $table = 'orden_detalle';
    protected $primaryKey = 'idorden_detalle';
    public $timestamps = false;

    protected $fillable = [
        'idordencompra',
        'idproducto',
        'cantidad',
        'precio_compra',
        'iva10',
        'iva5',
        'gravada10',
        'gravada5',
        'exenta',
        'items',
        'montoitems',
    ];
}
