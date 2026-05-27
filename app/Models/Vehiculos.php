<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculos extends Model
{
    use HasAuditTrail;

    use HasFactory;

    protected $table = 'vehiculo';
    protected $primaryKey = 'idvehiculo';
    public $timestamps = false;

    protected $fillable = [
        'nrochapa',
        'color',
        'chasis',
        'modelo',
    ];
    protected $guarded=[
        
    ];
}
