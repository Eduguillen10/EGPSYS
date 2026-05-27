<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComprasFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario' => 'required|max:100',
            'idsucursal' => 'required|integer',
            'idproveedor' => 'required|integer|exists:proveedores,idproveedor',
            'iddeposito' => 'required|integer|exists:depositos,iddeposito',
            'fecha' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha',
            'fecha_factura' => 'required|date|before_or_equal:fecha_vencimiento',
            'estado' => 'nullable|max:50',
            'ruc' => 'required|string|max:20',
            'nro_factura' => 'required|string|max:50',
            'condicion' => 'required|string|max:30',
            'totalcompra' => 'nullable|numeric|min:0',
            'idproducto.*' => 'required|integer|exists:productos,idproducto',
            'cantidad.*' => 'required|numeric|min:1',
            'precio_compra.*' => 'required|numeric|min:0',
            'items.*' => 'required|integer|min:1',
            'totalitems.*' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'usuario.required' => 'El campo usuario es obligatorio.',
            'idsucursal.required' => 'La sucursal es obligatoria.',
            'idproveedor.required' => 'El proveedor es obligatorio.',
            'idproveedor.exists' => 'El proveedor no existe en el sistema.',
            'iddeposito.required' => 'El depósito es obligatorio.',
            'iddeposito.exists' => 'El depósito no existe en el sistema.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha no tiene un formato válido.',
            'fecha_vencimiento.required' => 'La fecha de vencimiento es obligatoria.',
            'fecha_vencimiento.date' => 'La fecha de vencimiento no tiene un formato válido.',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la fecha de la compra.',
            'fecha_factura.required' => 'La fecha de la factura es obligatoria.',
            'fecha_factura.date' => 'La fecha de la factura no tiene un formato válido.',
            'fecha_factura.before_or_equal' => 'La fecha de la factura no puede ser posterior a la fecha de vencimiento.',
            'ruc.required' => 'El RUC es obligatorio.',
            'nro_factura.required' => 'El número de factura es obligatorio.',
            'idproducto.*.required' => 'Debe seleccionar al menos un producto.',
            'idproducto.*.exists' => 'El producto seleccionado no existe.',
            'cantidad.*.required' => 'La cantidad es obligatoria para cada producto.',
            'cantidad.*.numeric' => 'La cantidad debe ser un número válido.',
            'cantidad.*.min' => 'La cantidad debe ser mayor o igual a 1.',
            'precio_compra.*.required' => 'El precio de compra es obligatorio.',
            'precio_compra.*.numeric' => 'El precio de compra debe ser un número válido.',
            'precio_compra.*.min' => 'El precio de compra debe ser mayor o igual a 0.',
        ];
    }
}