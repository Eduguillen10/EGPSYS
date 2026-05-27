<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Model;

class Chofer extends Model
{
    use HasAuditTrail;

    protected $table = 'chofer';
    protected $primaryKey = 'idchofer';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellido',
        'ci',
        'ruc',
        'direccion',
        'telefono',
        'email',
    ];
    protected $guarded=[
            
    ];
}
