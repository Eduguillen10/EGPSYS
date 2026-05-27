<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperturaDetalle extends Model
{
    use HasAuditTrail;

    protected $table="apertura_detalle";

    protected $primaryKey="idaperturadet";

    public $timestamps = false;

    protected $fillable=[
        'idapertura',
        'items',
        'observacion',
        'id_formacobro',
        'monto_ingresado',
        'importe_calculado',
        'monto_cierre'
    ];

    protected $guarded=[
        
    ];
}
