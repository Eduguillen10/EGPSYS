<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrdenComprasFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'usuario'=> 'required|max:100',
            'idsucursal'=> 'required',
            'idproveedor'=> 'required',
            'iddeposito'=> 'required',
            'fecha'=> 'required',
            'estado'=>'max:50',
            'ruc'=> 'required',
            'total_orden_compra',
            'idproducto'=> 'required',
            'cantidad'=> 'required',
            'precio_compra'=> 'required',
        ];
    }
    
    public function messages()
    {
        return [
            'idproveedor.required' => 'El proveedor es obligatorio.',
            'idproveedor.exists' => 'El proveedor no existe.',
            'iddeposito.required' => 'El deposito es obligatorio.',
            'iddeposito.exists' => 'El deposito no existe.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha no tiene un formato válido.',
            'ruc.required' => 'El Ruc es obligatorio.',
            'observacion.max' => 'La observación no puede tener más de 255 caracteres.',
            'productos.required' => 'Debe seleccionar al menos un producto.',
            'productos.*.idproducto.required' => 'El producto es obligatorio.',
            'productos.*.idproducto.exists' => 'El producto no existe.',
            'productos.*.cantidad.required' => 'La cantidad es obligatoria.',
            'productos.*.cantidad.numeric' => 'La cantidad debe ser un número.',
            'productos.*.cantidad.min' => 'La cantidad debe ser mayor o igual a 1.',
            'productos.*.precio.required' => 'El precio es obligatorio.',
            'productos.*.precio.numeric' => 'El precio debe ser un número.',
            'productos.*.precio.min' => 'El precio debe ser mayor o igual a 0.',
        ];
    }
}
