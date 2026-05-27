<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntidadEmisora extends Model
{
    use HasAuditTrail;

    protected $table = "entidademisora";

    protected $primaryKey = "identidademisora";

    public $timestamps = false;

    protected $fillable = [
        'descripcion',
    ];

    protected $guarded = [
        
    ];
}
