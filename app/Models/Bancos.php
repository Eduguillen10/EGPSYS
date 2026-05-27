<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bancos extends Model
{
    use HasAuditTrail;

    protected $table="bancos";
    
        protected $primaryKey="idbanco";
    
        public $timestamps = false;
    
        protected $fillable=[
            
            'descripcion',
            
        ];
    
        protected $guarded=[
            
        ];
}
