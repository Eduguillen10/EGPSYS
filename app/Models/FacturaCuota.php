<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaCuota extends Model
{
    use HasAuditTrail;

    protected $table="factura_cuota";

    protected $primaryKey="idfactura_cuota";

    public $timestamps = false;

    protected $fillable=[
        
        'idproducto_cliente',
        'fecha_factura',
        'monto_total'
        
    ];

    protected $guarded=[
        
    ];
}
