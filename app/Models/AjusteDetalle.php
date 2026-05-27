<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjusteDetalle extends Model
{
    use HasAuditTrail;

    use HasFactory;
    protected $table="ajustes_productos_detalle";

    protected $primaryKey="idajustesproductos_detalle";

    public $timestamps = false;

    protected $fillable=[
        'idproducto',
        'idajusteproducto',
        'items',
        'cantidad'
    ];

        // Relación con el ajuste de producto (Cabecera)
        public function ajusteProducto()
        {
            return $this->belongsTo(AjusteProducto::class, 'idajusteproducto', 'idajusteproducto');
        }
    
        // Relación con el producto
        public function producto()
        {
            return $this->belongsTo(Producto::class, 'idproducto', 'idproducto');
        }

    protected $guarded=[
        
    ];
}
