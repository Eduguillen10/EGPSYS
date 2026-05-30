<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PresupuestosComprasFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idpedidocompra' => 'required|integer|exists:pedidos_compras,idpedidocompra',
            'idproveedor' => 'required|integer|exists:proveedores,idproveedor',
            'observacion' => 'nullable|string|max:100',
            'idproducto' => 'required|array|min:1',
            'idproducto.*' => 'required|integer|exists:productos,idproducto',
            'cantidad' => 'required|array|min:1',
            'cantidad.*' => 'required|numeric|min:1',
            'precio_compra' => 'required|array|min:1',
            'precio_compra.*' => 'required|numeric|min:1',
            'fechavalidez' => 'required|date|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'idpedidocompra.required' => 'Debe seleccionar un pedido de compra.',
            'idpedidocompra.exists' => 'El pedido seleccionado no existe.',
            'idproveedor.required' => 'El proveedor es obligatorio.',
            'idproveedor.exists' => 'El proveedor no existe.',
            'observacion.max' => 'La observacion no debe superar 100 caracteres.',
            'idproducto.required' => 'Debe agregar al menos un producto.',
            'cantidad.required' => 'Debe cargar la cantidad del producto.',
            'cantidad.*.min' => 'La cantidad debe ser mayor o igual a 1.',
            'precio_compra.required' => 'Debe cargar el precio de compra.',
            'precio_compra.*.required' => 'Todos los productos deben tener precio de compra.',
            'precio_compra.*.numeric' => 'El precio de compra debe ser numerico.',
            'precio_compra.*.min' => 'El precio de compra debe ser mayor a 0.',
            'fechavalidez.after_or_equal' => 'La fecha de validez debe ser igual o posterior a la fecha actual.',
        ];
    }
}
