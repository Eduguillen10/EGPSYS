<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marcas extends Model
{
    use HasAuditTrail;

    protected $table="marcas";
    
        protected $primaryKey="idmarca";
    
        public $timestamps = false;
    
        protected $fillable=[
            
            'descripcion',
            
        ];
    
        protected $guarded=[
            
        ];
}
