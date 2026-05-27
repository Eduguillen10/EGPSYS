<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaCobrar extends Model
{
    use HasAuditTrail;

     protected $table="cuenta_cobrar";

    protected $primaryKey="idcuenta_cobrar";

    public $timestamps = false;

    protected $fillable=[
        
        'idcliente',
        'idsucursal',
        'idventa',        
        'fecha_vencimiento',
        'condicion',
        'estado',
        'fecha',
        'saldo',
        'importe'
          
     ];
}
