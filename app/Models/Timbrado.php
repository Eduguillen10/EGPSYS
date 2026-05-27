<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timbrado extends Model
{
    use HasAuditTrail;

    use HasFactory;

    protected $table="timbrado";

    protected $primaryKey="idtimbrado";

    public $timestamps = false;

    protected $fillable=[
        
        'nro_inicial',
        'nro_final',
        'nro_actual',
        'nro_serie',
        'fecha_inicial',
        'estado',
        'nro_timbrado',
        'idsucursal',
        'fecha_vencimiento',
        'tipo'
    ];

    protected $guarded=[
        
    ];
}
