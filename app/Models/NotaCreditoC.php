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
        'timbrado',
        'fecha_registro',
        'fecha_factura',
        'fecha_vencimiento',
        'concepto',        
        'estado',
        'idusuario',
        'montoiva10',
        'montoiva5',
        'montogravada10',
        'montogravada5',
        'montoexenta',
        'montonota_credito_compra'
    ];
}
