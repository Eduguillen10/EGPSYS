<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoImpuesto extends Model
{
    use HasAuditTrail;

    use HasFactory;

    protected $table = 'tipo_impuesto';
    protected $primaryKey = 'idtipoimpuesto';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
    ];
    protected $guarded=[
        
    ];
}
