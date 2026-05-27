<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursales extends Model
{
    use HasAuditTrail;

    protected $table = "sucursales";
    
    protected $primaryKey = "idsucursal";

    public $timestamps = false;

    protected $fillable = [
        'idempresa',
        'descripcion',
    ];

    protected $guarded = [
        
    ];

    public function depositos()
{
    return $this->hasMany(Depositos::class, 'idsucursal', 'idsucursal');
}

}
