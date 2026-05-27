<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiposDocumentos extends Model
{
    use HasAuditTrail;

    use HasFactory;

    protected $table = 'tipos_documentos';
    protected $primaryKey = 'idtipodocumento';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
    ];
    protected $guarded=[
        
    ];
}
