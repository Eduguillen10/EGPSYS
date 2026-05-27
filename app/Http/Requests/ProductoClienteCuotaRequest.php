<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductoClienteCuotaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "idproducto_cliente"=> "required",
            "cuota"=> "required",
            "refuerzo"=> "required",
            "fecha_vto_cuota"=> "required",
            "monto_cuota"=> "required",
            "monto_interes_mora"=> "required",
            "monto_interes_punitorio"=> "required",
            "saldo_cuota"=> "required",
        ];
    }
}
