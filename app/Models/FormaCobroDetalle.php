<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormaCobroDetalle extends Model
{
    use HasFactory, HasAuditTrail;

    protected $table="det_formacobro";

    protected array $auditSensitive = [
        'numero_tarjeta',
        'nro_tarjeta',
        'cvv',
        'token_pago',
    ];

    protected $primaryKey="id_detformacobro";

    public $timestamps = false;

    protected $fillable=[
        
        'id_cobro',
        'id_formacobro',
        'items',
        'monto_detformacobro',
        'documento',
        'idtipodocumento',
        'fecha',
        'fecha_vencimiento',
        'id_entidad',
        'identidademisora',
        'monto_recibido',
        'vuelto',
    ];
}
