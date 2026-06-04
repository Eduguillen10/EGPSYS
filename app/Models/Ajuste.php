<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;

class Ajuste extends Model
{
    use HasAuditTrail;

    protected $table = 'ajuste';

    protected $primaryKey = 'idajuste';

    public $timestamps = false;

    protected $fillable = [
        'idsucursal',
        'iddeposito',
        'idtipo_ajuste',
        'idmotivo',
        'idusuario',
        'fecha',
        'observacion',
        'estado',
    ];

    public function detalles()
    {
        return $this->hasMany(AjusteDetalle::class, 'idajuste', 'idajuste');
    }

    public function tipoAjuste()
    {
        return $this->belongsTo(TipoAjuste::class, 'idtipo_ajuste', 'idtipo_ajuste');
    }

    public function motivo()
    {
        return $this->belongsTo(Motivo::class, 'idmotivo', 'idmotivo');
    }
}
