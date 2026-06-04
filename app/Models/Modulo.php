<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    use HasAuditTrail;

    protected $table = 'modulos';

    protected $primaryKey = 'idmodulo';

    protected $fillable = [
        'nombre',
        'icono',
        'orden',
        'estado',
    ];

    public function ventanas()
    {
        return $this->hasMany(Ventana::class, 'idmodulo', 'idmodulo');
    }

    public static function menuParaUsuario(User $user)
    {
        return self::query()
            ->where('estado', true)
            ->whereHas('ventanas.permisosUsuario', function ($query) use ($user) {
                $query->where('idusuario', $user->id)
                    ->whereHas('accion', function ($accion) {
                        $accion->where('clave', 'ver');
                    });
            })
            ->with(['ventanas' => function ($query) use ($user) {
                $query->where('estado', true)
                    ->whereHas('permisosUsuario', function ($permiso) use ($user) {
                        $permiso->where('idusuario', $user->id)
                            ->whereHas('accion', function ($accion) {
                                $accion->where('clave', 'ver');
                            });
                    })
                    ->orderBy('orden');
            }])
            ->orderBy('orden')
            ->get();
    }
}
