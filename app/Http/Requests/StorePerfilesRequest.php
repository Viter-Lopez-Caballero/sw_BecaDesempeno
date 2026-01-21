<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePerfilesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return[
            'name' => "required|max:255|unique:roles",
            'guard_name' => 'required',
            'description'=> "required",
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nombre del Rol',
            'guard_name' => 'Guarda',
            'description' => "Descripción",
        ];
    }
}