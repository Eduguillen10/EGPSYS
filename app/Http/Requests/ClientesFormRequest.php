<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesReferentialData;
use Illuminate\Foundation\Http\FormRequest;

class ClientesFormRequest extends FormRequest
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
            'idtipo_cliente' => 'nullable|integer|exists:tipo_cliente,idtipo_cliente',
            'idnacionalidad' => 'required|integer|exists:nacionalidades,idnacionalidad',
            'idtipodocumento' => 'required|integer|exists:tipos_documentos,idtipodocumento',
            'nombre' => $this->personNameRules(100),
            'num_documento' => $this->documentRules('clientes', 'num_documento', 'idcliente'),
            'direccion' => $this->optionalBasicTextRules(150),
            'telefono' => $this->phoneRules(),
            'email' => [
                'nullable',
                'string',
                'email',
                'max:100',
                $this->uniqueIgnoringCurrent('clientes', 'email', 'idcliente'),
            ],
            'modo_clasificacion' => 'nullable|in:manual,automatico',
        ];
    }
}
