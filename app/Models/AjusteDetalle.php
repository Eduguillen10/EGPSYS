<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Model;

class AjusteDetalle extends Model
{
    use HasAuditTrail;

    protected $table = 'ajuste_detalle';

    protected $primaryKey = 'idajuste_detalle';

    public $timestamps = false;

    protected $fillable = [
        'idajuste',
        'idproducto',
        'items',
        'cantidad',
    ];
}
