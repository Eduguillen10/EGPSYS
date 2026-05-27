<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuloFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|max:100',
            'icono' => 'nullable|max:80',
            'orden' => 'required|integer|min:0',
            'estado' => 'nullable|boolean',
        ];
    }
}
