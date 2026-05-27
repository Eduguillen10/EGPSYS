<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AjusteFormRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        return true; // Cambia a `false` si quieres restringir el acceso
    }

    /**
     * Reglas de validación para ajustes de productos y sus detalles.
     */
    public function rules(): array
    {
        return [
            // Validaciones para la tabla "ajustes_productos"
            'idsucursal' => 'required|integer|exists:sucursales,idsucursal',
            'iddeposito' => 'required|integer|exists:depositos,iddeposito',
            'fecha' => 'required|date',
            'tipoajuste' => 'required|string|in:Entrada,Salida',
            'idmotivo' => 'required|integer|exists:motivos,idmotivo',
            'usuario' => 'required|string|max:100',

            // Validaciones para la tabla "ajustes_productos_detalle"
            'detalles' => 'required|array|min:1',
            'detalles.*.idproducto' => 'required|integer|exists:productos,idproducto',
            'detalles.*.items' => 'required|integer|min:1',
            'detalles.*.cantidad' => 'required|integer|min:1',
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

            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'Debe ser una fecha válida.',

            'tipoajuste.required' => 'El tipo de ajuste es obligatorio.',
            'tipoajuste.in' => 'El tipo de ajuste debe ser "Entrada" o "Salida".',

            'idmotivo.required' => 'El motivo del ajuste es obligatorio.',
            'idmotivo.exists' => 'El motivo seleccionado no es válido.',

            'usuario.required' => 'El usuario es obligatorio.',
            'usuario.max' => 'El usuario no puede superar los 100 caracteres.',

            'detalles.required' => 'Debe haber al menos un detalle en el ajuste.',
            'detalles.array' => 'Los detalles deben ser un arreglo válido.',

            'detalles.*.idproducto.required' => 'Cada detalle debe tener un producto.',
            'detalles.*.idproducto.exists' => 'El producto seleccionado no existe.',

            'detalles.*.items.required' => 'Cada detalle debe indicar el número de ítems.',
            'detalles.*.items.min' => 'El número de ítems debe ser al menos 1.',

            'detalles.*.cantidad.required' => 'Cada detalle debe indicar la cantidad.',
            'detalles.*.cantidad.min' => 'La cantidad debe ser al menos 1.',
        ];
    }
}

