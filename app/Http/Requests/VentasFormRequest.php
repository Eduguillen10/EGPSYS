<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VentasFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'idcliente'=>'required',
            'idtimbrado'=>'required',
            'idsucursal'=>'required',
            'iddeposito'=>'required',
            'razon_social'=>'required|max:100',
            'num_documento'=>'max:10',
            //'nro_factura'=>'required|max:20',
            'idproducto'=>'required',
            'cantidad'=>'required',
            'precio_venta'=>'required',
            'condicion'=>'required',
            'total_venta'=>'required'
        ];
    }

     /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'idsucursal.required' => 'El campo sucursal es obligatorio.',
            'idsucursal.exists' => 'La sucursal seleccionada no existe.',

            'iddeposito.required' => 'El campo depósito es obligatorio.',
            'iddeposito.exists' => 'El depósito seleccionado no existe.',

            'idtimbrado.required' => 'El timbrado es obligatorio.',
            'idtimbrado.exists' => 'El timbrado seleccionado no existe.',

            'idcliente.required' => 'El cliente es obligatorio.',
            'idcliente.exists' => 'El cliente seleccionado no existe.',

            'fecha.required' => 'La fecha de la venta es obligatoria.',
            'fecha.date' => 'Debe ser una fecha válida.',

            'num_documento.required' => 'El número de documento es obligatorio.',
            'num_documento.unique' => 'El número de documento ya existe en el sistema.',

            'nro_factura.required' => 'El número de factura es obligatorio.',
            'nro_factura.unique' => 'El número de factura ya está registrado.',

            'estado.required' => 'El estado de la venta es obligatorio.',
        ];
    }
}
