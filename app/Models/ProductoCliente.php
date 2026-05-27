<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoCliente extends Model
{
    use HasAuditTrail;

    protected $table="producto_cliente";

    protected $primaryKey="idproducto_cliente";

    public $timestamps = false;

    protected $fillable=[
        
        'idsucursal',
        'iddeposito',
        'fecha',
        'fecha_vencimiento',
        'idcondicion',
        'cta_cte',
        'idcliente',
        'idproducto',
        'concepto',
        'porcentaje_interes',
        'entrega',
        'total_venta',
        'total_saldo',
        'total_interes_moratorio',
        'total_interes_punitorio'
    ];

    protected $guarded=[
        
    ];
}
