<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cobro extends Model
{
    use HasAuditTrail;

    protected $table="cobros";

    protected array $auditSensitive = [
        'numero_tarjeta',
        'nro_tarjeta',
        'cvv',
        'token_pago',
    ];

    protected $primaryKey="id_cobro";

    public $timestamps = false;

    protected $fillable=[
        
        'idapertura',
        'fecha_cobro',
        'monto_cobro',
        'cobro_estado',
        'idusuario',
        'idsucursal',
        'idcliente',
        'idcaja',
        'hash_documento',
        'hash_anulacion',
        'hash_version',
     ];

    protected $guarded=[
        
    ];
}
