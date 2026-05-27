<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;

class Ventana extends Model
{
    use HasAuditTrail;

    protected $table = 'ventanas';

    protected $primaryKey = 'idventana';

    protected $fillable = [
        'idmodulo',
        'nombre',
        'ruta',
        'permiso_clave',
        'orden',
        'estado',
    ];

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'idmodulo', 'idmodulo');
    }

    public function permisosUsuario()
    {
        return $this->hasMany(UsuarioPermiso::class, 'idventana', 'idventana');
    }
}
