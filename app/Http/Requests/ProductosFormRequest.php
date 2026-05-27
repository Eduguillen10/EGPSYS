<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesReferentialData;
use Illuminate\Foundation\Http\FormRequest;

class ProductosFormRequest extends FormRequest
{
    use ValidatesReferentialData;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => $this->alphaNumericCodeRules('productos', 'codigo', 'idproducto', 50),
            'idrubro' => 'required|integer|exists:rubros,idrubro',
            'idmarca' => 'required|integer|exists:marcas,idmarca',
            'idtipoimpuesto' => 'required|integer|exists:tipo_impuesto,idtipoimpuesto',
            'descripcion' => $this->descriptionRules('productos', 'idproducto', 250),
            'precio_compra' => 'required|integer|min:1',
            'precio_venta' => 'required|integer|min:1|gte:precio_compra',
            'tipo_producto' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^(?=.*[\pL])[\pL\pN\s.\-\/]+$/u',
            ],
            'estado' => 'nullable|in:Activo,Inactivo',
        ];
    }
}
