<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apertura extends Model
{
    use HasAuditTrail;

    protected $table="apertura";

    protected $primaryKey="idapertura";

    public $timestamps = false;

    protected $fillable=[
        'idcaja',
        'monto_inicial',
        'monto_cierre',
        'fecha_apertura',
        'estado',
        'fecha_cierre',
        'idusuario',
        'idtipoarqueo',
        'idsucursal'
    ];

    protected $guarded=[
    ];
}
