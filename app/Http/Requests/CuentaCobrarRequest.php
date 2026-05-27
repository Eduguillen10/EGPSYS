<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CuentaCobrarRequest extends FormRequest
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
            'idcliente'=>'required',
            'idventa'=>'required',
            'importe'=>'required',
            'saldo'=>'required',
            'fecha_vencimiento'=>'required',
            'fecha'=>'required'
        ];
    }
}
