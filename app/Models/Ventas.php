<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ventas extends Model
{
    use HasFactory, HasAuditTrail;

    protected $table = 'ventas'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'idventa'; // Clave primaria
    public $timestamps = false;


    protected $fillable = [
        'idsucursal',
        'iddeposito',
        'idtimbrado',
        'idcliente',
        'fecha',
        'razon_social',
        'num_documento',
        'nro_factura',
        'cta_cte_cliente',
        'totaliva10',
        'totaliva5',
        'totalgravada10',
        'totalgravada5',
        'totalexenta',
        'totalventa',
        'obs',
        'saldo_factura',
        'condicion',
        'estado',
        'usuario',
    ];

    // Relación con Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'idcliente', 'idcliente');
    }

    // Relación con Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'idsucursal', 'idsucursal');
    }

    // Relación con Depósito
    public function deposito()
    {
        return $this->belongsTo(Deposito::class, 'iddeposito', 'iddeposito');
    }

    // Relación con los detalles de venta
    public function detalles()
    {
        return $this->hasMany(VentasDetalle::class, 'idventa', 'idventa');
    }
}
