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
            'idcompra' => 'required|integer|exists:compras,idcompra',
            'fecha_factura' => 'required|date',
            'nro_factura' => 'required|string|max:20',
            'timbrado' => 'required|string|max:20',
            'concepto' => 'required|string|max:100',
            'idproducto' => 'required|array|min:1',
            'idproducto.*' => 'required|integer|exists:productos,idproducto',
            'cantidad' => 'required|array|min:1',
            'cantidad.*' => 'required|numeric|min:1',
            'precio_compra' => 'required|array|min:1',
            'precio_compra.*' => 'required|numeric|min:1',
        ];
    }

    public function messages()
    {
        return [
            'idcompra.required' => 'Debe seleccionar una compra.',
            'fecha_factura.required' => 'Debe seleccionar la fecha del comprobante.',
            'nro_factura.required' => 'Debe ingresar el numero de comprobante de la nota de credito.',
            'timbrado.required' => 'Debe ingresar el timbrado.',
            'concepto.required' => 'Debe ingresar el concepto de la devolucion.',
            'idproducto.required' => 'Debe agregar al menos un producto al detalle.',
            'cantidad.*.min' => 'La cantidad devuelta debe ser mayor a cero.',
            'precio_compra.*.min' => 'El precio de compra debe ser mayor a cero.',
        ];
    }
}
