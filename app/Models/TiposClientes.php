<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiposClientes extends Model
{
    use HasAuditTrail;

    use HasFactory;

    protected $table = 'tipo_cliente';
    protected $primaryKey = 'idtipo_cliente';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
    ];
    protected $guarded=[
        
    ];
}
