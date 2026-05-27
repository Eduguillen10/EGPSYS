<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidosComprasDetalle extends Model
{
    use HasAuditTrail;

    protected $table = 'pedidos_compras_detalle';
    protected $primaryKey = 'idpedidocompra_detalle';
    public $timestamps = false;

    protected $fillable = [
        'idpedidocompra',
        'idproducto',
        'cantidad',
        'items',
    ];
}
