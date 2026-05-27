<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotaCreditoCRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'idsucursal'=>'required',
            'iddeposito'=>'required',
            'idproveedor'=>'required',
            'ruc'=>'required|max:10',
            'usuario'=>'required',            
            'idproducto'=>'required',
            'cantidad'=>'required',
            'precio_compra'=>'required'
                        
        ];
    }
}

