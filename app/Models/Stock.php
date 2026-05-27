<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stock';

    protected $primaryKey = ['iddeposito', 'idproducto'];

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'iddeposito',
        'idproducto',
        'idsucursal',
        'cantidad',
    ];

    protected $guarded = [

    ];
}
