<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductoClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            
            'idsucursal' => 'required',
            'iddeposito' => 'required',
            'fecha' => 'required',
            'fecha_vencimiento' => 'required',
            'idcondicion' => 'required',
            'idcliente' => 'required',
            'idproducto' => 'required',
            'codeudor' => 'required',
            'total_cuotas' => 'required',
            'porcentaje_interes' => 'required',
            'entrega' => 'required',
            'total_venta' => 'required',
            'total_saldo' => 'required',
            'total_interes_moratorio' => 'required',
            'total_interes_punitorio' => 'required',
            
        ];
    }
}
