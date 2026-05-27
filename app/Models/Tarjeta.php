<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarjeta extends Model
{
    use HasAuditTrail;

    protected $table="tarjeta";

    protected $primaryKey="id_tarjeta";

    public $timestamps = false;

    protected $fillable=[
        
        'descripcion',
        'identidademisora'
    ];

    protected $guarded=[
        
    ];
}
