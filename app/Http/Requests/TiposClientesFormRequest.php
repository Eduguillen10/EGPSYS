<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesReferentialData;
use Illuminate\Foundation\Http\FormRequest;

class TiposClientesFormRequest extends FormRequest
{
    use ValidatesReferentialData;

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'descripcion' => $this->descriptionRules('tipo_cliente', 'idtipo_cliente', 100),
        ];
    }
}
