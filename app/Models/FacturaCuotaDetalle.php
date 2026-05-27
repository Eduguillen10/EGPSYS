<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaCuotaDetalle extends Model
{
    use HasAuditTrail;

    protected $table="factura_cuota_detalle";

    protected $primaryKey="idfactura_cuota_detalle";

    public $timestamps = false;

    protected $fillable=[
        
        'idfactura_cuota',
        'idproducto_cliente_cuota'
        
    ];

    protected $guarded=[
        
    ];
}
