<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compras extends Model
{
    use HasAuditTrail;

    protected $table="compras";

    protected $primaryKey="idcompra";

    public $timestamps = false;

    protected $fillable=[
        'idordencompra',
        'idsucursal',
        'idusuario',
        'iddeposito',
        'idproveedor',
        'ruc',        
        'nro_factura',
        'condicion',
        'fecha',
        'fecha_factura',
        'fecha_vencimiento',
        'concepto',        
        'estado',
        'montoiva10',
        'montoiva5',
        'montogravada10',
        'montogravada5',
        'montoexenta',
        'montocompra',
        'timbrado',
    ];
}
