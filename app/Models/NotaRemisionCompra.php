<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;

class NotaRemisionCompra extends Model
{
    use HasAuditTrail;

    protected $table = 'nota_remision_compra';
    protected $primaryKey = 'idremisionc';
    public $timestamps = false;

    protected $fillable = [
        'idordencompra',
        'idproveedor',
        'iddeposito',
        'idsucursal',
        'idusuario',
        'nro_comprobante',
        'fecha',
        'fecha_remision',
        'motivo_traslado',
        'chofer',
        'documento_chofer',
        'vehiculo',
        'chapa',
        'observacion',
        'estado',
    ];

    public function detalles()
    {
        return $this->hasMany(NotaRemisionCompraDetalle::class, 'idremisionc', 'idremisionc');
    }

    public function orden()
    {
        return $this->belongsTo(OrdenCompras::class, 'idordencompra', 'idordencompra');
    }
}
