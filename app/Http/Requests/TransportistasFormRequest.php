<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesReferentialData;
use Illuminate\Foundation\Http\FormRequest;

class TransportistasFormRequest extends FormRequest
{
    use ValidatesReferentialData;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'min:2',
                'max:150',
                'regex:/^(?=.*[\pL])[\pL\pN\s.,\-\/()&]+$/u',
            ],
            'documento' => [
                'nullable',
                'string',
                'min:5',
                'max:50',
                'regex:/^[A-Za-z0-9.\-]+$/',
                $this->uniqueIgnoringCurrent('transportistas', 'documento', 'idtransportista'),
            ],
            'direccion' => $this->optionalBasicTextRules(180),
            'telefono' => $this->phoneRules(),
            'email' => [
                'nullable',
                'string',
                'email',
                'max:100',
                $this->uniqueIgnoringCurrent('transportistas', 'email', 'idtransportista'),
            ],
        ];
    }
}
