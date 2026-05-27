<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaCreditoC extends Model
{
    use HasAuditTrail;

    protected $table="nota_credito_compra";

    protected $primaryKey="idnota_creditoc";

    public $timestamps = false;

    protected $fillable=[
        'idcompra',
        'idsucursal',
        'iddeposito',
        'idproveedor',
        'ruc',        
        'nro_factura',
        'condicion',
        'fecha_registro',
        'fecha_factura',
        'fecha_vencimiento',
        'concepto',        
        'estado',
        'usuario',
        'totaliva10',
        'totaliva5',
        'totalgravada10',
        'totalgravada5',
        'totalexenta',
        'totalcompra'
    ];
}
