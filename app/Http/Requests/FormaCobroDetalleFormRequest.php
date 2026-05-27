<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormaCobroDetalleFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_cobro' => 'required',
            'id_formacobro' => 'required',
            'items' => 'required',
            'monto_detformacobro' => 'required',
            'sec_detalleformacobro' => 'required'
        ];
    }
}
