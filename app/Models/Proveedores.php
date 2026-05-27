<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Model;

class Proveedores extends Model
{
    use HasAuditTrail;

    protected $table = 'proveedores';
    protected $primaryKey = 'idproveedor';
    public $timestamps = false;

    protected $fillable = [
        'idciudad',
        'razonsocial',
        'ruc',
        'direccion',
        'telefono',
    ];

    public function ciudad()
    {
        return $this->belongsTo(Ciudades::class, 'idciudad', 'idciudad');
    }
}
