<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasAuditTrail;

    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    
    protected $table='users';

    protected $primaryKey='id';
    
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function permisos()
    {
        return $this->hasMany(UsuarioPermiso::class, 'idusuario', 'id');
    }

    public function sucursales()
    {
        return $this->belongsToMany(Sucursales::class, 'usuario_sucursal', 'idusuario', 'idsucursal')
            ->withPivot('idempresa');
    }

    public function empresas()
    {
        return $this->belongsToMany(Empresas::class, 'usuario_sucursal', 'idusuario', 'idempresa')
            ->withPivot('idsucursal');
    }

    public function tienePermiso(string $ventanaClave, string $accionClave = 'ver'): bool
    {
        return $this->permisos()
            ->whereHas('ventana', function ($ventana) use ($ventanaClave) {
                $ventana->where('permiso_clave', $ventanaClave)
                    ->where('estado', true);
            })
            ->whereHas('accion', function ($accion) use ($accionClave) {
                $accion->where('clave', $accionClave);
            })
            ->exists();
    }
}
