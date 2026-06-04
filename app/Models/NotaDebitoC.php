<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;

class NotaDebitoC extends Model
{
    use HasAuditTrail;

    protected $table = 'nota_debito_compra';

    protected $primaryKey = 'idnota_debitoc';

    public $timestamps = false;

    protected $fillable = [
        'idcompra',
        'idsucursal',
        'iddeposito',
        'idproveedor',
        'idusuario',
        'ruc',
        'nro_nota_debito',
        'timbrado',
        'fecha_registro',
        'fecha_factura',
        'fecha_vencimiento',
        'concepto',
        'montoiva10',
        'montoiva5',
        'montogravada10',
        'montogravada5',
        'montoexenta',
        'montonota_debito_compra',
        'mueve_stock',
        'estado',
    ];
}
