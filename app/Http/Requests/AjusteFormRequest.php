<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AjusteFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idsucursal' => 'required|integer|exists:sucursales,idsucursal',
            'iddeposito' => 'required|integer|exists:depositos,iddeposito',
            'fecha' => 'required|date',
            'idtipo_ajuste' => 'required|integer|exists:tipo_ajuste,idtipo_ajuste',
            'idmotivo' => 'required|integer|exists:motivo,idmotivo',
            'observacion' => 'nullable|string|max:255',
            'idproducto' => 'required|array|min:1',
            'idproducto.*' => 'required|integer|exists:productos,idproducto',
            'cantidad' => 'required|array|min:1',
            'cantidad.*' => 'required|numeric|min:0.001',
        ];
    }

    public function messages(): array
    {
        return [
            'idsucursal.required' => 'La sucursal es obligatoria.',
            'iddeposito.required' => 'Debe seleccionar un deposito.',
            'iddeposito.exists' => 'El deposito seleccionado no existe.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha no tiene un formato valido.',
            'idtipo_ajuste.required' => 'Debe seleccionar el tipo de ajuste.',
            'idtipo_ajuste.exists' => 'El tipo de ajuste seleccionado no existe.',
            'idmotivo.required' => 'Debe seleccionar el motivo del ajuste.',
            'idmotivo.exists' => 'El motivo seleccionado no existe.',
            'idproducto.required' => 'Debe agregar al menos un producto.',
            'idproducto.*.exists' => 'Uno de los productos seleccionados no existe.',
            'cantidad.*.required' => 'Debe ingresar la cantidad de cada producto.',
            'cantidad.*.numeric' => 'La cantidad debe ser numerica.',
            'cantidad.*.min' => 'La cantidad debe ser mayor a cero.',
        ];
    }
}
