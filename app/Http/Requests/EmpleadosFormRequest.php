<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesReferentialData;
use Illuminate\Foundation\Http\FormRequest;

class EmpleadosFormRequest extends FormRequest
{
    use ValidatesReferentialData;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idciudad' => 'required|integer|exists:ciudades,idciudad',
            'idcargo' => 'required|integer|exists:cargos,idcargo',
            'nombre' => $this->personNameRules(50),
            'apellido' => $this->personNameRules(50),
            'ci' => $this->documentRules('empleados', 'ci', 'idempleado'),
            'direccion' => $this->optionalBasicTextRules(100),
            'telefono' => $this->phoneRules(),
            'estado' => 'required|in:Activo,Inactivo',
        ];
    }
}
