<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Libroventas extends Model
{
    use HasAuditTrail;

    protected $table="libro_venta";

    protected $primaryKey="idlibro_venta";

    public $timestamps = false;

    protected $fillable=[
        
        'idventa',
        'idcliente',
        'num_docuento',
        'nro_factura',
        'fecha_factura',
        'timbrado',
        'totaliva10',
        'totaliva5',
        'totalgravada10',
        'totalgravada5',
        'totalexenta',
        'totalventa'        
    ];

    protected $guarded=[
        
    ];
}
