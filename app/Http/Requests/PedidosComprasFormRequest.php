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
            'observacion' => 'nullable|string|max:200',
            'idproducto' => 'required|array|min:1',
            'idproducto.*' => 'required|integer|exists:productos,idproducto',
            'cantidad' => 'required|array|min:1',
            'cantidad.*' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'idproducto.required' => 'Debe agregar al menos un producto al pedido.',
            'idproducto.array' => 'El detalle de productos no tiene un formato valido.',
            'idproducto.min' => 'Debe agregar al menos un producto al pedido.',
            'idproducto.*.exists' => 'Uno de los productos seleccionados no existe.',
            'cantidad.required' => 'Debe ingresar la cantidad del producto.',
            'cantidad.array' => 'El detalle de cantidades no tiene un formato valido.',
            'cantidad.*.min' => 'La cantidad debe ser mayor a cero.',
            'observacion.max' => 'La observacion no debe superar 200 caracteres.',
        ];
    }
}
