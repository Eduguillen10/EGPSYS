<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentasDetalle extends Model
{
    use HasFactory, HasAuditTrail;

    protected $table = 'venta_detalle'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'idventa_detalle'; // Clave primaria
    public $timestamps = false; 

    protected $fillable = [
        'idventa',
        'idproducto',
        'items',
        'cantidad',
        'precio_venta',
        'iva10',
        'iva5',
        'gravada10',
        'gravada5',
        'exenta',
        'montoitems',
    ];

    // Relación con Venta (muchos detalles pertenecen a una venta)
    public function venta()
    {
        return $this->belongsTo(Ventas::class, 'idventa', 'idventa');
    }

    // Relación con Producto (cada detalle pertenece a un producto)
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idproducto', 'idproducto');
    }
}
