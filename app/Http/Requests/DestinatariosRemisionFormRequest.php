<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesReferentialData;
use Illuminate\Foundation\Http\FormRequest;

class DestinatariosRemisionFormRequest extends FormRequest
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
                $this->uniqueIgnoringCurrent('destinatarios_remision', 'documento', 'iddestinatario_remision'),
            ],
            'direccion' => $this->optionalBasicTextRules(180),
            'telefono' => $this->phoneRules(),
            'email' => [
                'nullable',
                'string',
                'email',
                'max:100',
                $this->uniqueIgnoringCurrent('destinatarios_remision', 'email', 'iddestinatario_remision'),
            ],
        ];
    }
}
