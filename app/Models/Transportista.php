<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;

class Transportista extends Model
{
    use HasAuditTrail;

    protected $table = 'transportistas';
    protected $primaryKey = 'idtransportista';
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
