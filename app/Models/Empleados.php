<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Model;

class Empleados extends Model
{
    use HasAuditTrail;

    protected $table = 'empleados';
    protected $primaryKey = 'idempleado';
    public $timestamps = false;

    protected $fillable = [
        'idciudad',
        'idcargo',
        'nombre',
        'apellido',
        'ci',
        'direccion',
        'telefono',
        'estado',
    ];

    public function ciudad()
    {
        return $this->belongsTo(Ciudades::class, 'idciudad', 'idciudad');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargos::class, 'idcargo', 'idcargo');
    }
}
