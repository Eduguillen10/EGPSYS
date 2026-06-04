<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesReferentialData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TipoAjusteFormRequest extends FormRequest
{
    use ValidatesReferentialData;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'descripcion' => [
                'required',
                'string',
                Rule::in(['Entrada', 'Salida']),
                $this->uniqueIgnoringCurrent('tipo_ajuste', 'descripcion', 'idtipo_ajuste'),
            ],
        ];
    }
}
