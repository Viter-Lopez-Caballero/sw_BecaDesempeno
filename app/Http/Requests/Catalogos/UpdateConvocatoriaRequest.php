<?php

namespace App\Http\Requests\Catalogos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConvocatoriaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'anio' => ['required', 'integer', 'min:2000', 'max:2100'],
            'estado' => ['required', 'in:activa,cerrada,pendiente'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'anio.required' => 'El año es obligatorio.',
            'anio.integer' => 'El año debe ser un número entero.',
            'anio.min' => 'El año debe ser mayor o igual a 2000.',
            'anio.max' => 'El año debe ser menor o igual a 2100.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activa, cerrada o pendiente.',
        ];
    }
}
