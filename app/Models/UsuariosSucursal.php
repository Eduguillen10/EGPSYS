<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioSucursal extends Model
{
    use HasAuditTrail;

    protected $table="usuario_sucursal";

    protected $primaryKey="idusuariosucursal";

    public $timestamps = false;

    protected $fillable=[
        'idusuario',
        'idsucursal',
        'idempresa'        
    ];

    protected $guarded=[
        
    ];
}
