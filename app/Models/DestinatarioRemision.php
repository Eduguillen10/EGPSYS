<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;

class DestinatarioRemision extends Model
{
    use HasAuditTrail;

    protected $table = 'destinatarios_remision';
    protected $primaryKey = 'iddestinatario_remision';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'documento',
        'direccion',
        'telefono',
        'email',
        'estado',
    ];
}
