<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComprasDetalle extends Model
{
    use HasAuditTrail;

    protected $table="compra_detalle";

    protected $primaryKey="idcompra_detalle";

    public $timestamps = false;

    protected $fillable=[
        'idcompra',
        'items',
        'idproducto',
        'cantidad',
        'precio_compra',
        'iva10',
        'iva5',
        'gravada10',
        'gravada5',
        'exenta',
        'montoitems'
    ];

    protected $guarded=[
        
    ];
}
