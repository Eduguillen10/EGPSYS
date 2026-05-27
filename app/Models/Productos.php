<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    use HasAuditTrail;

    protected $table = 'productos';
    protected $primaryKey = 'idproducto';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'idrubro',
        'idmarca',
        'idtipoimpuesto',
        'descripcion',
        'precio_compra',
        'precio_venta',
        'tipo_producto',
        'estado',
    ];

    public function rubro()
    {
        return $this->belongsTo(Rubros::class, 'idrubro', 'idrubro');
    }

    public function marca()
    {
        return $this->belongsTo(Marcas::class, 'idmarca', 'idmarca');
    }

    public function tipoimpuesto()
    {
        return $this->belongsTo(TipoImpuesto::class, 'idtipoimpuesto', 'idtipoimpuesto');
    }
}
