<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rubros extends Model
{
    use HasAuditTrail;

    protected $table="rubros";
    
    protected $primaryKey="idrubro";

    public $timestamps = false;

    protected $fillable=[
        
        'descripcion',
        
    ];

    protected $guarded=[
        
    ];
}
