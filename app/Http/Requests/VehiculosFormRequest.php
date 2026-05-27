<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesReferentialData;
use Illuminate\Foundation\Http\FormRequest;

class VehiculosFormRequest extends FormRequest
{
    use ValidatesReferentialData;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nrochapa' => $this->alphaNumericCodeRules('vehiculo', 'nrochapa', 'idvehiculo', 15),
            'color' => $this->personNameRules(75),
            'chasis' => $this->alphaNumericCodeRules('vehiculo', 'chasis', 'idvehiculo', 50),
            'modelo' => [
                'required',
                'string',
                'min:2',
                'max:150',
                'regex:/^(?=.*[\pL\pN])[\pL\pN\s.\-\/]+$/u',
            ],
        ];
    }
}
