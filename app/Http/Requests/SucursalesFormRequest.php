<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesReferentialData;
use Illuminate\Foundation\Http\FormRequest;

class SucursalesFormRequest extends FormRequest
{
    use ValidatesReferentialData;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idempresa' => 'required|integer|exists:empresas,idempresa',
            'descripcion' => $this->descriptionRules('sucursales', 'idsucursal', 150),
        ];
    }
}
