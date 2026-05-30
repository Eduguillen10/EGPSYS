<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;

class NotaRemisionVentaDetalle extends Model
{
    use HasAuditTrail;

    protected $table = 'nota_remision_venta_detalle';
    protected $primaryKey = 'idnota_remision_venta_detalle';

    protected $fillable = [
        'idnota_remision_venta',
        'idproducto',
        'items',
        'cantidad',
    ];
}
