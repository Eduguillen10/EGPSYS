<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresupuestosCompras extends Model
{
    use HasAuditTrail;

    protected $table = 'presupuestos_compras';
    protected $primaryKey = 'idpresupuestocompra';
    public $timestamps = false;

    protected $fillable = [
        'idproveedor',
        'idsucursal',
        'idusuario',
        'idpedidocompra',
        'fecha',
        'observacion',
        'estado',
        'fechavalidez',
    ];
}






















