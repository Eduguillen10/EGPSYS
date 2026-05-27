<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciudades extends Model
{
    use HasAuditTrail;

    protected $table="ciudades";
    
        protected $primaryKey="idciudad";
    
        public $timestamps = false;
    
        protected $fillable=[
            
            'descripcion',
            
        ];
    
        protected $guarded=[
            
        ];
}
