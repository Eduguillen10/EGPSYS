<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;

class VentaCreditoAceptacion extends Model
{
    use HasAuditTrail;

    protected $table = 'venta_credito_aceptaciones';
    protected $primaryKey = 'idaceptacion_credito';

    protected $fillable = [
        'idventa',
        'idcliente',
        'metodo_aceptacion',
        'recibido_por',
        'documento_receptor',
        'telefono_receptor',
        'relacion_receptor',
        'monto',
        'condicion',
        'fecha_vencimiento',
        'texto_aceptado',
        'archivo_respaldo',
        'archivo_nombre_original',
        'hash_documento',
        'hash_anulacion',
        'hash_version',
        'ip',
        'user_agent',
        'estado',
        'usuario',
        'observacion',
    ];

    public function venta()
    {
        return $this->belongsTo(Ventas::class, 'idventa', 'idventa');
    }

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'idcliente', 'idcliente');
    }
}
