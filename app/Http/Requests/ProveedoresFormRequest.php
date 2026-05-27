<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesReferentialData;
use Illuminate\Foundation\Http\FormRequest;

class ProveedoresFormRequest extends FormRequest
{
    use ValidatesReferentialData;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idciudad' => 'required|integer|exists:ciudades,idciudad',
            'razonsocial' => [
                'required',
                'string',
                'min:2',
                'max:80',
                'regex:/^(?=.*[\pL\pN])[\pL\pN\s.,&\-\/()]+$/u',
                $this->uniqueIgnoringCurrent('proveedores', 'razonsocial', 'idproveedor'),
            ],
            'ruc' => $this->paraguayRucRules('proveedores', 'ruc', 'idproveedor'),
            'direccion' => $this->optionalBasicTextRules(100),
            'telefono' => $this->phoneRules(),
        ];
    }
}
