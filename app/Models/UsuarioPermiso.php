<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioPermiso extends Model
{
    protected $table = 'usuario_permisos';

    protected $primaryKey = 'idusuariopermiso';

    protected $fillable = [
        'idusuario',
        'idventana',
        'idaccion',
    ];

    public function accion()
    {
        return $this->belongsTo(Accion::class, 'idaccion', 'idaccion');
    }

    public function ventana()
    {
        return $this->belongsTo(Ventana::class, 'idventana', 'idventana');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'idusuario', 'id');
    }
}
