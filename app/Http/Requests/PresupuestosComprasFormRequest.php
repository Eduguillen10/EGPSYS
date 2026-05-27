<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PresupuestosComprasFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'usuario'=> 'required|max:100',
            'idsucursal'=> 'required',
            'idproveedor'=> 'required',
            'fecha'=> 'required',
            'estado'=>'max:10',
            'totalpedido',
            'idproducto'=> 'required',
            'cantidad'=> 'required',
            'fechavalidez' => 'required|date|after_or_equal:today',
        ];
    }

    public function messages()
    {
        return [
            'idproveedor.required' => 'El proveedor es obligatorio.',
            'idproveedor.exists' => 'El proveedor no existe.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha no tiene un formato válido.',
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



