<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotaRemisionCompraFormRequest extends FormRequest
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
            'idordencompra' => 'required|integer|exists:orden_compras,idordencompra',
            'nro_comprobante' => 'required|string|max:30',
            'fecha_remision' => 'required|date',
            'motivo_traslado' => 'required|string|max:100',
            'chofer' => 'nullable|string|max:100',
            'documento_chofer' => 'nullable|string|max:30',
            'vehiculo' => 'nullable|string|max:100',
            'chapa' => 'nullable|string|max:30',
            'observacion' => 'nullable|string|max:255',
            'idorden_detalle' => 'required|array|min:1',
            'idorden_detalle.*' => 'required|integer|exists:orden_detalle,idorden_detalle',
            'idproducto' => 'required|array|min:1',
            'idproducto.*' => 'required|integer|exists:productos,idproducto',
            'cantidad' => 'required|array|min:1',
            'cantidad.*' => 'required|numeric|min:0.001',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'idordencompra.required' => 'Debe seleccionar una orden de compra.',
            'idordencompra.exists' => 'La orden de compra seleccionada no existe.',
            'nro_comprobante.required' => 'Debe ingresar el numero de comprobante de la nota de remision.',
            'fecha_remision.required' => 'Debe seleccionar la fecha de remision.',
            'fecha_remision.date' => 'La fecha de remision no tiene un formato valido.',
            'motivo_traslado.required' => 'Debe ingresar el motivo de traslado.',
            'idorden_detalle.required' => 'Debe cargar al menos un producto en el detalle.',
            'idorden_detalle.*.exists' => 'Uno de los detalles de la orden no existe.',
            'idproducto.required' => 'Debe cargar al menos un producto.',
            'idproducto.*.exists' => 'Uno de los productos seleccionados no existe.',
            'cantidad.required' => 'Debe ingresar la cantidad recibida.',
            'cantidad.*.numeric' => 'La cantidad debe ser numerica.',
            'cantidad.*.min' => 'La cantidad debe ser mayor a cero.',
        ];
    }
}
