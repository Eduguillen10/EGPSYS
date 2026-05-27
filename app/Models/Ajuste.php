<?php

namespace App\Models;

use App\Models\Concerns\HasAuditTrail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ajuste extends Model
{
    use HasAuditTrail;

    use HasFactory;

    protected $table="ajustes_productos";

    protected $primaryKey="idajusteproducto";

    public $timestamps = false;

    protected $fillable=[
        'idsucursal',
        'iddeposito',
        'fecha',
        'tipoajuste',
        'idmotivo',
        'usuario'   
    ];

        // Definir valores permitidos para tipoajuste
        public const TIPO_ENTRADA = 'Entrada';
        public const TIPO_SALIDA = 'Salida';
    
        public static function tiposAjuste()
        {
            return [
                self::TIPO_ENTRADA => 'Entrada',
                self::TIPO_SALIDA => 'Salida',
            ];
        }
    
        // Método para verificar si un tipo de ajuste es válido
        public static function esTipoAjusteValido($tipo)
        {
            return in_array($tipo, array_keys(self::tiposAjuste()));
        }

    // Relación con la sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'idsucursal', 'idsucursal');
    }

    // Relación con el depósito
    public function deposito()
    {
        return $this->belongsTo(Deposito::class, 'iddeposito', 'iddeposito');
    }

    // Relación con el motivo del ajuste
    public function motivo()
    {
        return $this->belongsTo(Motivo::class, 'idmotivo', 'idmotivo');
    }

    // Relación con los detalles del ajuste
    public function detalles()
    {
        return $this->hasMany(AjusteProductoDetalle::class, 'idajusteproducto', 'idajusteproducto');
    }

    protected $guarded=[
        
    ];
}
