<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LibroVentasRequest extends FormRequest
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
            'idfactura_venta'=>'required',
            'idcliente'=>'required',
            'num_documento'=>'required',
            'nro_factura'=>'required',
            'fecha_factura'=>'required',
            'timbrado'=>'required'
        ];
    }
}
