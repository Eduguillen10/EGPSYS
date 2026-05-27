<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

trait ValidatesReferentialData
{
    protected function prepareForValidation(): void
    {
        $fields = [
            'descripcion',
            'nombre',
            'apellido',
            'razonsocial',
            'num_documento',
            'ruc',
            'ci',
            'direccion',
            'telefono',
            'email',
            'nrochapa',
            'color',
            'chasis',
            'modelo',
            'codigo',
            'nro_serie',
            'nro_timbrado',
        ];

        $data = [];

        foreach ($fields as $field) {
            if (! $this->has($field) || is_null($this->input($field))) {
                continue;
            }

            $data[$field] = preg_replace('/\s+/', ' ', trim((string) $this->input($field)));
        }

        if ($data !== []) {
            $this->merge($data);
        }
    }

    protected function uniqueIgnoringCurrent(string $table, string $column, string $primaryKey)
    {
        $rule = Rule::unique($table, $column);
        $id = $this->routeId();

        return $id ? $rule->ignore($id, $primaryKey) : $rule;
    }

    protected function descriptionRules(string $table, string $primaryKey, int $max = 100): array
    {
        return [
            'required',
            'string',
            'min:2',
            'max:' . $max,
            'regex:/^(?=.*[\pL])[\pL\pN\s.,\-\/()%]+$/u',
            $this->uniqueIgnoringCurrent($table, 'descripcion', $primaryKey),
        ];
    }

    protected function personNameRules(int $max = 100): array
    {
        return [
            'required',
            'string',
            'min:2',
            'max:' . $max,
            'regex:/^[\pL\s.\'-]+$/u',
        ];
    }

    protected function optionalBasicTextRules(int $max = 150): array
    {
        return [
            'nullable',
            'string',
            'max:' . $max,
            'regex:/^(?=.*[\pL\pN])[\pL\pN\s.,#\-\/()]+$/u',
        ];
    }

    protected function paraguayRucRules(string $table, string $column, string $primaryKey): array
    {
        return [
            'required',
            'string',
            'min:7',
            'max:10',
            'regex:/^\d{5,8}-\d$/',
            $this->uniqueIgnoringCurrent($table, $column, $primaryKey),
        ];
    }

    protected function documentRules(string $table, string $column, string $primaryKey): array
    {
        return [
            'required',
            'string',
            'digits_between:5,15',
            $this->uniqueIgnoringCurrent($table, $column, $primaryKey),
        ];
    }

    protected function phoneRules(): array
    {
        return [
            'nullable',
            'string',
            'min:6',
            'max:20',
            'regex:/^[0-9+\-\s()]+$/',
        ];
    }

    protected function alphaNumericCodeRules(string $table, string $column, string $primaryKey, int $max = 50): array
    {
        return [
            'required',
            'string',
            'min:2',
            'max:' . $max,
            'regex:/^[A-Za-z0-9\-]+$/',
            $this->uniqueIgnoringCurrent($table, $column, $primaryKey),
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'integer' => 'El campo :attribute debe ser un numero entero.',
            'numeric' => 'El campo :attribute debe ser numerico.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',
            'max' => 'El campo :attribute no debe superar :max caracteres.',
            'digits_between' => 'El campo :attribute debe contener entre :min y :max digitos.',
            'email' => 'Ingrese un correo electronico valido.',
            'unique' => 'Ya existe un registro con ese valor en :attribute.',
            'exists' => 'El valor seleccionado en :attribute no existe.',
            'in' => 'El valor seleccionado en :attribute no es valido.',
            'descripcion.regex' => 'La descripcion solo puede contener letras, numeros, espacios y signos basicos como punto, coma, guion, barra, parentesis y porcentaje.',
            'nombre.regex' => 'El nombre solo puede contener letras, espacios, punto, apostrofe o guion.',
            'apellido.regex' => 'El apellido solo puede contener letras, espacios, punto, apostrofe o guion.',
            'razonsocial.regex' => 'La razon social solo puede contener letras, numeros, espacios y signos basicos.',
            'ruc.regex' => 'El RUC debe tener el formato 1234567-8. No se permiten valores como 1, * ni caracteres especiales.',
            'ci.regex' => 'La cedula debe contener solo numeros.',
            'num_documento.regex' => 'El numero de documento debe contener solo numeros.',
            'telefono.regex' => 'El telefono solo puede contener numeros, espacios, parentesis, + o guion.',
            'direccion.regex' => 'La direccion solo puede contener letras, numeros, espacios y signos basicos.',
            'codigo.regex' => 'El codigo solo puede contener letras, numeros y guion.',
            'nrochapa.regex' => 'La chapa solo puede contener letras, numeros y guion.',
            'chasis.regex' => 'El chasis solo puede contener letras, numeros y guion.',
        ];
    }

    public function attributes(): array
    {
        return [
            'descripcion' => 'descripcion',
            'razonsocial' => 'razon social',
            'num_documento' => 'numero de documento',
            'ci' => 'cedula',
            'ruc' => 'RUC',
            'idciudad' => 'ciudad',
            'idcargo' => 'cargo',
            'idempresa' => 'empresa',
            'idsucursal' => 'sucursal',
            'identidademisora' => 'entidad emisora',
            'idrubro' => 'rubro',
            'idmarca' => 'marca',
            'idtipoimpuesto' => 'tipo de impuesto',
            'nrochapa' => 'chapa',
            'nro_timbrado' => 'numero de timbrado',
            'nro_serie' => 'serie',
        ];
    }

    private function routeId(): mixed
    {
        foreach ($this->route()?->parameters() ?? [] as $value) {
            if ($value instanceof Model) {
                return $value->getKey();
            }

            if (is_scalar($value)) {
                return $value;
            }
        }

        return null;
    }
}
