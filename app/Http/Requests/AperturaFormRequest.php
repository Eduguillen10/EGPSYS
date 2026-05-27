<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AperturaFormRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     */
    public function authorize()
    {
        return true; // Cambia esto si necesitas permisos específicos.
    }

    /**
     * Reglas de validación para la apertura de caja.
     */
    public function rules()
    {
        return [
            'monto_inicial' => 'required|numeric|min:0',
            'monto_cierre' => 'nullable|numeric|min:0',
            'fecha_apertura' => 'required|date',
            'fecha_cierre' => 'nullable|date|after_or_equal:fecha_apertura',
            'estado' => 'nullable',
            'idcaja' => 'required|integer|exists:cajas,idcaja',
            'id' => 'required|integer|exists:users,id',
            'idsucursal' => 'required|integer|exists:sucursales,idsucursal',
            'usuario' => 'required|string|max:100',
            'idtipoarqueo' => 'integer',

            // Validaciones para los detalles de la apertura
            'detalles.*.id_formacobro' => 'required|integer|exists:formacobro,id_formacobro',
            'detalles.*.items' => 'required|integer|min:1',
            'detalles.*.observacion' => 'nullable|string|max:255',
            'detalles.*.monto_ingresado' => 'required|numeric|min:0',
        ];
    }

    /**
     * Mensajes personalizados para los errores de validación.
     */
    public function messages()
    {
        return [
            'monto_inicial.required' => 'El monto inicial es obligatorio.',
            'monto_inicial.numeric' => 'El monto inicial debe ser un número.',
            'fecha_apertura.required' => 'La fecha de apertura es obligatoria.',
            'fecha_apertura.date' => 'La fecha de apertura debe ser válida.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser "Abierto" o "Cerrado".',
            'idcaja.exists' => 'La caja seleccionada no es válida.',
            'id.exists' => 'El usuario no existe.',
            'idsucursal.exists' => 'La sucursal no es válida.',
            'usuario.required' => 'El usuario es obligatorio.',
            'detalles.*.id_formacobro.exists' => 'La forma de cobro no es válida.',
        ];
    }
}