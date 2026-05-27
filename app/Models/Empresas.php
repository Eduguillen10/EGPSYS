<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresas extends Model
{
    use HasAuditTrail;

    protected $table = "empresas";
    
    protected $primaryKey = "idempresa";

    public $timestamps = false;

    protected $fillable = [
        'descripcion',
    ];

    protected $guarded = [
        
    ];
}
