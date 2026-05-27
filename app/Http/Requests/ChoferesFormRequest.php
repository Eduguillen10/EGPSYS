<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesReferentialData;
use Illuminate\Foundation\Http\FormRequest;

class ChoferesFormRequest extends FormRequest
{
    use ValidatesReferentialData;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => $this->personNameRules(100),
            'apellido' => $this->personNameRules(100),
            'ci' => $this->documentRules('chofer', 'ci', 'idchofer'),
            'ruc' => $this->paraguayRucRules('chofer', 'ruc', 'idchofer'),
            'direccion' => $this->optionalBasicTextRules(200),
            'telefono' => $this->phoneRules(),
            'email' => [
                'nullable',
                'string',
                'email',
                'max:100',
                $this->uniqueIgnoringCurrent('chofer', 'email', 'idchofer'),
            ],
        ];
    }
}
