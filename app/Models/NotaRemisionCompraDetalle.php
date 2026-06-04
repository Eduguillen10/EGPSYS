<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;

class NotaRemisionCompraDetalle extends Model
{
    use HasAuditTrail;

    protected $table = 'nota_remision_compra_detalle';
    protected $primaryKey = 'idremisionc_detalle';
    public $timestamps = false;

    protected $fillable = [
        'idremisionc',
        'idorden_detalle',
        'idproducto',
        'items',
        'cantidad',
    ];

    public function remision()
    {
        return $this->belongsTo(NotaRemisionCompra::class, 'idremisionc', 'idremisionc');
    }
}
