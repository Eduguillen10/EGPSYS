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
        'id',
        'estado',
    ];

    protected $casts = [
        'id' => 'integer', // Convierte id a entero
    ];

    // Relación con la tabla users (usuario que creó la caja)
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id');
    }

    // Scope para filtrar solo cajas activas
    public function scopeActivas($query)
    {
        return $query->where('estado', 'Activo');
    }

    protected $guarded = [

    ];
}
