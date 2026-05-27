<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesReferentialData;
use Illuminate\Foundation\Http\FormRequest;

class TarjetaFormRequest extends FormRequest
{
    use ValidatesReferentialData;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'descripcion' => $this->descriptionRules('tarjeta', 'id_tarjeta', 150),
            'identidademisora' => 'required|integer|exists:entidademisora,identidademisora',
        ];
    }
}
