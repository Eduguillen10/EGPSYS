<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motivo extends Model
{
    use HasAuditTrail;

    protected $table = "motivo";
    
    protected $primaryKey = "idmotivo";

    public $timestamps = false;

    protected $fillable = [
        'descripcion',
    ];

    protected $guarded = [];
}
