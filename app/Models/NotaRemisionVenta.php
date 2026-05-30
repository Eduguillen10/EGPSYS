<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;

class NotaRemisionVenta extends Model
{
    use HasAuditTrail;

    protected $table = 'nota_remision_venta';
    protected $primaryKey = 'idnota_remision_venta';

    protected $fillable = [
        'idventa',
        'idcliente',
        'iddestinatario_remision',
        'tipo_origen',
        'idchofer',
        'idvehiculo',
        'idtransportista',
        'idtimbrado',
        'iddeposito_origen',
        'iddeposito_destino',
        'nro_remision',
        'fecha_emision',
        'fecha_traslado',
        'fecha_inicio_traslado',
        'fecha_fin_traslado',
        'motivo_traslado',
        'punto_partida',
        'punto_llegada',
        'ciudad_partida',
        'departamento_partida',
        'ciudad_llegada',
        'departamento_llegada',
        'recibido_por',
        'documento_receptor',
        'fecha_entrega',
        'hora_entrega',
        'observacion_entrega',
        'usuario_recepcion',
        'recepcion_registrada_at',
        'estado',
        'usuario',
        'observacion',
        'motivo_anulacion',
        'fecha_anulacion',
        'hash_documento',
        'hash_recepcion',
        'hash_anulacion',
        'hash_version',
    ];

    public function destinatarioRemision()
    {
        return $this->belongsTo(DestinatarioRemision::class, 'iddestinatario_remision', 'iddestinatario_remision');
    }

    public function transportista()
    {
        return $this->belongsTo(Transportista::class, 'idtransportista', 'idtransportista');
    }
}
