<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CobroDetalle extends Model
{
    use HasAuditTrail;

    protected $table="det_cobro";

    protected $primaryKey="id_detcobro";

    public $timestamps = false;

    protected $fillable=[
        
        'id_cobro',
        'idventa',
        'items',
        'monto_detcobro'         
     ];

    protected $guarded=[
        
    ];
}
