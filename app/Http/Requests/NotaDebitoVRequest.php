<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotaDebitoVRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idsucursal' => ['required', 'integer', 'exists:sucursales,idsucursal'],
            'iddeposito' => ['required', 'integer', 'exists:depositos,iddeposito'],
            'idcliente' => ['required', 'integer', 'exists:clientes,idcliente'],
            'num_documento' => ['required', 'string', 'max:15'],
            'idventa' => ['required', 'integer', 'exists:ventas,idventa'],
            'nro_factura' => ['required', 'string', 'max:50'],
            'timbrado' => ['required', 'string', 'max:10'],
            'concepto' => ['required', 'string', 'max:100'],
            'fecha_factura' => ['required', 'date'],
            'idproducto' => ['required', 'array', 'min:1'],
            'idproducto.*' => ['required', 'integer', 'exists:productos,idproducto'],
            'cantidad' => ['required', 'array', 'min:1'],
            'cantidad.*' => ['required', 'integer', 'min:1'],
            'precio_venta' => ['required', 'array', 'min:1'],
            'precio_venta.*' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'idventa.required' => 'Debe seleccionar una factura de venta.',
            'idproducto.required' => 'Debe cargar al menos un producto.',
            'cantidad.*.min' => 'La cantidad debe ser mayor a cero.',
            'precio_venta.*.min' => 'El precio de venta debe ser mayor a cero.',
            'timbrado.max' => 'El timbrado no puede superar 10 caracteres.',
            'concepto.max' => 'El concepto no puede superar 100 caracteres.',
        ];
    }
}
