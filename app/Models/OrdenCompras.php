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
        'idpresupuestocompra',
        'idproveedor',
        'iddeposito',
        'fecha',
        'estado',
        'usuario',
        'ruc',
        'direccion',
        'observacion',
        'totaliva10',
        'totaliva5',
        'totalgravada10',
        'totalgravada5',
        'totalexenta',
        'total_orden_compra',
    ];
}
