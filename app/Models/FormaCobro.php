<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormaCobro extends Model
{
    use HasAuditTrail;

    protected $table = "formacobro";

    protected $primaryKey = "id_formacobro";

    public $timestamps = false;

    protected $fillable = [
        'descripcion',
    ];

    protected $guarded = [
        
    ];
}
