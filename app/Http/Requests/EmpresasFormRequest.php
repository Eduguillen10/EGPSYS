<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesReferentialData;
use Illuminate\Foundation\Http\FormRequest;

class EmpresasFormRequest extends FormRequest
{
    use ValidatesReferentialData;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'descripcion' => $this->descriptionRules('empresas', 'idempresa', 150),
        ];
    }
}
