<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoClienteCuota extends Model
{
    use HasAuditTrail;

    protected $table="producto_cliente_cuota";

    protected $primaryKey="idproducto_cliente_cuota";

    public $timestamps = false;

    protected $fillable=[
        
        'idproducto_cliente',
        'cuota',
        'refuerzo',
        'fecha_vto_cuota',
        'monto_cuota',
        'monto_interes_mora',
        'monto_interes_punitorio',
        'saldo_cuota'
    ];

    protected $guarded=[
        
    ];
}
