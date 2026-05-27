<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Model;

class Cargos extends Model
{
    use HasAuditTrail;

    protected $table = 'cargos';
    protected $primaryKey = 'idcargo';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
    ];
    
    protected $guarded=[
            
    ];
}
