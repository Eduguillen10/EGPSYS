<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cajas extends Model
{
    use HasAuditTrail;

    use HasFactory;

    protected $table = "cajas";
    protected $primaryKey = "idcaja";
    public $timestamps = false; // Se activa para manejar fechas de creación y actualización

    protected $fillable = [
        'descripcion',
        'idusuario',
        'estado',
    ];

    protected $casts = [
        'idusuario' => 'integer',
    ];

    // Relación con la tabla users (usuario que creó la caja)
    public function usuario()
    {
        return $this->belongsTo(User::class, 'idusuario');
    }

    // Scope para filtrar solo cajas activas
    public function scopeActivas($query)
    {
        return $query->where('estado', 'Activo');
    }

    protected $guarded = [

    ];
}
