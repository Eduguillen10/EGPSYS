<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;

class Accion extends Model
{
    use HasAuditTrail;

    protected $table = 'acciones';

    protected $primaryKey = 'idaccion';

    protected $fillable = [
        'nombre',
        'clave',
        'orden',
    ];
}
