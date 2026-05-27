<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depositos extends Model
{
    use HasAuditTrail;

    protected $table = "depositos";
    
    protected $primaryKey = "iddeposito";

    public $timestamps = false;

    protected $fillable = [
        'idsucursal',
        'descripcion',
    ];

    protected $guarded = [
        
    ];

    public function sucursal()
{
    return $this->belongsTo(Sucursales::class, 'idsucursal', 'idsucursal');
}

}
