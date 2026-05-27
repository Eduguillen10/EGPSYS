<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidosCompras extends Model
{
    use HasAuditTrail;

    protected $table = 'pedidos_compras';
    protected $primaryKey = 'idpedidocompra';
    public $timestamps = false;

    protected $fillable = [
        'usuario',
        'idsucursal',
        'fecha',
        'observacion',
        'estado',
        'total_pedido',
    ];
}
