<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenCompras extends Model
{
    use HasAuditTrail;

    protected $table = 'orden_compras';
    protected $primaryKey = 'idordencompra';
    public $timestamps = false;

    protected $fillable = [
        'idsucursal',
        'idusuario',
        'idpresupuestocompra',
        'idproveedor',
        'iddeposito',
        'fecha',
        'estado',
        'ruc',
        'direccion',
        'observacion',
        'montoiva10',
        'montoiva5',
        'montogravada10',
        'montogravada5',
        'montoexenta',
        'monto_orden_compra',
    ];
}
