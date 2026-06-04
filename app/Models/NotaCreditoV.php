<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaCreditoV extends Model
{
    use HasAuditTrail;

    protected $table="nota_credito_venta";

    protected $primaryKey="idnota_creditov";

    public $timestamps = false;

    protected $fillable=[
        'idventa',
        'idsucursal',
        'iddeposito',
        'idcliente',
        'num_documento',        
        'nro_nota_credito',
        'nro_factura',
        'condicion',
        'fecha_registro',
        'fecha_factura',
        'fecha_vencimiento',
        'concepto',        
        'estado',
        'idusuario',
        'timbrado',
        'totaliva10',
        'totaliva5',
        'totalgravada10',
        'totalgravada5',
        'totalexenta',
        'totalventa',
        'hash_documento',
        'hash_anulacion',
        'hash_version',
    ];
}
