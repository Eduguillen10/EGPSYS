<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesReferentialData;
use Illuminate\Foundation\Http\FormRequest;

class TimbradoFormRequest extends FormRequest
{
    use ValidatesReferentialData;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nro_inicial' => 'required|integer|min:1',
            'nro_final' => 'required|integer|gte:nro_inicial',
            'nro_actual' => 'required|integer|min:1|gte:nro_inicial|lte:nro_final',
            'nro_serie' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[A-Za-z0-9\-]+$/',
            ],
            'idsucursal' => 'required|integer|exists:sucursales,idsucursal',
            'fecha_inicial' => 'required|date',
            'nro_timbrado' => [
                'required',
                'string',
                'digits_between:6,15',
                $this->uniqueIgnoringCurrent('timbrado', 'nro_timbrado', 'idtimbrado'),
            ],
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_inicial',
            'estado' => 'nullable|in:Activo,Inactivo',
        ];
    }
}
