<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Clientes extends Model
{
    use HasAuditTrail;

    protected $table = 'clientes';
    protected $primaryKey = 'idcliente';
    public $timestamps = false;

    protected $fillable = [
        'idciudad',
        'idtipo_cliente',
        'idnacionalidad',
        'idtipodocumento',
        'nombre',
        'num_documento',
        'direccion',
        'telefono',
        'email',
        'modo_clasificacion'
    ];

    public function nacionalidad()
    {
        return $this->belongsTo(Ciudades::class, 'idnacionalidad', 'idnacionalidad');
    }

    public function ciudad()
    {
        return $this->belongsTo(Ciudades::class, 'idciudad', 'idciudad');
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(Ciudades::class, 'idtipodocumento', 'idtipo_documento');
    }

    public function tipoCliente()
    {
        return $this->belongsTo(TiposClientes::class, 'idtipo_cliente', 'idtipo_cliente');
    }


    public function getClasificacionAttribute()
    {
        // Si ya tiene un tipo de cliente asignado manualmente, usar ese.
        if (!is_null($this->idtipo_cliente)) {
            return $this->tipoCliente->descripcion ?? 'Desconocido';
        }

         // Si no tiene asignación manual, calcular según las ventas.
        $totalVentas = DB::table('ventas')
            ->where('idcliente', $this->idcliente)
            ->sum('totalventa');

        if ($totalVentas > 100000000) {
            return 'Platino';
        } elseif ($totalVentas >= 50000000) {
            return 'Oro';
        } elseif ($totalVentas >= 10000000) {
            return 'Plata';
        } else {
            return 'Bronce';
        }
    }
}
