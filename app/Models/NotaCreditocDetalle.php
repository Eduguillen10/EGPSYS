<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaCreditocDetalle extends Model
{
    use HasAuditTrail;

    protected $table="nota_credito_compra_detalle";

    protected $primaryKey="idnota_creditoc_detalle";

    public $timestamps = false;

    protected $fillable=[
        'idnota_creditoc',
        'items',
        'idproducto',
        'cantidad',
        'precio_compra',
        'iva10',
        'iva5',
        'gravada10',
        'gravada5',
        'exenta',
        'totalitems'
    ];
}
