<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaCreditovDetalle extends Model
{
    use HasAuditTrail;

    protected $table="nota_credito_venta_detalle";

    protected $primaryKey="idnota_creditov_detalle";

    public $timestamps = false;

    protected $fillable=[
        'idnota_creditov',
        'items',
        'idproducto',
        'cantidad',
        'precio_venta',
        'iva10',
        'iva5',
        'gravada10',
        'gravada5',
        'exenta',
        'totalitems'
    ];
}
