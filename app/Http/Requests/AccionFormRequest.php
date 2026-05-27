<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccionFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $idaccion = $this->routeId();

        return [
            'nombre' => 'required|max:100',
            'clave' => [
                'required',
                'max:80',
                'alpha_dash',
                Rule::unique('acciones', 'clave')->ignore($idaccion, 'idaccion'),
            ],
            'orden' => 'required|integer|min:0',
        ];
    }

    private function routeId(): ?int
    {
        foreach (['accion', 'acciones', 'accione', 'id'] as $parameter) {
            $value = $this->route($parameter);

            if ($value) {
                return (int) $value;
            }
        }

        return null;
    }
}
