<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;

class TipoAjuste extends Model
{
    use HasAuditTrail;

    protected $table = 'tipo_ajuste';

    protected $primaryKey = 'idtipo_ajuste';

    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'estado',
    ];
}
