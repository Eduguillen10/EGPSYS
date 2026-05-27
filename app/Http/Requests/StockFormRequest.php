<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'iddeposito' => 'required|numeric|exists:depositos,iddeposito',
            'idproducto' => 'required|numeric|exists:productos,idproducto',
            'cantidad' => 'numeric',
        ];
    }
}
