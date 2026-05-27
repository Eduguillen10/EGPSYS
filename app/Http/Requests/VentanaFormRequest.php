<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VentanaFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $idventana = $this->routeId();

        return [
            'idmodulo' => 'required|exists:modulos,idmodulo',
            'nombre' => 'required|max:120',
            'ruta' => 'required|max:255',
            'permiso_clave' => [
                'required',
                'max:120',
                'alpha_dash',
                Rule::unique('ventanas', 'permiso_clave')->ignore($idventana, 'idventana'),
            ],
            'orden' => 'required|integer|min:0',
            'estado' => 'nullable|boolean',
        ];
    }

    private function routeId(): ?int
    {
        foreach (['ventana', 'ventanas', 'id'] as $parameter) {
            $value = $this->route($parameter);

            if ($value) {
                return (int) $value;
            }
        }

        return null;
    }
}
