<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PedidosComprasFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'usuario'=> 'required',
            'idsucursal'=> 'required',
            'observacion'=>'max:200',
            'estado'=>'max:10',
            'totalpedido',
            'idproducto'=> 'required',
            'cantidad'=> 'required',

        ];
    }
}
