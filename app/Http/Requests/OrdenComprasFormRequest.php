<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrdenComprasFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'idpresupuestocompra' => 'required|integer|exists:presupuestos_compras,idpresupuestocompra',
            'idproveedor' => 'required|integer|exists:proveedores,idproveedor',
            'iddeposito' => 'required|integer|exists:depositos,iddeposito',
            'ruc' => 'required|string|max:20',
            'direccion' => 'required|string|max:100',
            'observacion' => 'nullable|string|max:255',
            'idproducto' => 'required|array|min:1',
            'idproducto.*' => 'required|integer|exists:productos,idproducto',
            'cantidad' => 'required|array|min:1',
            'cantidad.*' => 'required|numeric|min:1',
            'precio_compra' => 'required|array|min:1',
            'precio_compra.*' => 'required|numeric|min:1',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'idpresupuestocompra.required' => 'Debe seleccionar un presupuesto de compra.',
            'idpresupuestocompra.exists' => 'El presupuesto seleccionado no existe.',
            'idproveedor.required' => 'El proveedor es obligatorio.',
            'idproveedor.exists' => 'El proveedor no existe.',
            'iddeposito.required' => 'El deposito es obligatorio.',
            'iddeposito.exists' => 'El deposito no existe.',
            'ruc.required' => 'El RUC es obligatorio.',
            'direccion.required' => 'La direccion del proveedor es obligatoria.',
            'observacion.max' => 'La observacion no puede tener mas de 255 caracteres.',
            'idproducto.required' => 'Debe seleccionar al menos un producto.',
            'idproducto.*.required' => 'El producto es obligatorio.',
            'idproducto.*.exists' => 'El producto no existe.',
            'cantidad.required' => 'Debe cargar la cantidad de los productos.',
            'cantidad.*.required' => 'La cantidad es obligatoria.',
            'cantidad.*.numeric' => 'La cantidad debe ser un numero.',
            'cantidad.*.min' => 'La cantidad debe ser mayor o igual a 1.',
            'precio_compra.required' => 'Debe cargar el precio de compra de los productos.',
            'precio_compra.*.required' => 'El precio de compra es obligatorio.',
            'precio_compra.*.numeric' => 'El precio de compra debe ser un numero.',
            'precio_compra.*.min' => 'El precio de compra debe ser mayor o igual a 1.',
        ];
    }
}
