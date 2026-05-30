<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresupuestosComprasDetalle extends Model
{
    use HasAuditTrail;

    protected $table = 'presupuestos_compras_detalle';
    protected $primaryKey = 'idpresupuestocompra_detalle';
    public $timestamps = false;

    protected $fillable = [
        'idpresupuestocompra',
        'idproducto',
        'cantidad',
        'precio',
        'items',
        'iva10',
        'iva5',
        'gravada10',
        'gravada5',
        'exenta',
        'montoitems',
    ];
}
