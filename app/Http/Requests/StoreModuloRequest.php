<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuloRequest extends FormRequest
{
    protected string $tableName = 'module';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return[
            'nombre'        => "required|max:255|unique:modules",
            'descripcion' => 'required|max:255',
            'key'         => "required|string|max:255|min:1|unique:modules,key",
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'Nombre del Modulo',
            'descripcion' => 'Descripcion',
            'key' => 'Clave'
        ];
    }
}
