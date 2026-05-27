<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesReferentialData;
use Illuminate\Foundation\Http\FormRequest;

class DepositosFormRequest extends FormRequest
{
    use ValidatesReferentialData;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idsucursal' => 'required|integer|exists:sucursales,idsucursal',
            'descripcion' => $this->descriptionRules('depositos', 'iddeposito', 50),
        ];
    }
}
