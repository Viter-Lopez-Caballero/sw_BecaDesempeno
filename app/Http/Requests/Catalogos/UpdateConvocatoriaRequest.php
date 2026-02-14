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
            'estado' => ['required', 'in:activa,cerrada,pendiente'],
            'archivo' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:30720'], // 30MB
            'imagen' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // 2MB

            // Calendar Validations (No check against 'today' for updates to allow editing running events)
            'publicacion_inicio' => ['required', 'date'],
            // 'publicacion_fin' removed
            'registro_inicio' => ['required', 'date', 'after_or_equal:publicacion_inicio'],
            'registro_fin' => ['required', 'date', 'after_or_equal:registro_inicio'],
            'evaluacion_inicio' => ['required', 'date', 'after_or_equal:registro_fin'],
            'evaluacion_fin' => ['required', 'date', 'after_or_equal:evaluacion_inicio'],
            'resultados_inicio' => ['required', 'date', 'after_or_equal:evaluacion_fin'],
            'resultados_fin' => ['required', 'date', 'after_or_equal:resultados_inicio'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activa, cerrada o pendiente.',
            'archivo.file' => 'El archivo debe ser un archivo válido.',
            'archivo.mimes' => 'El archivo debe ser de tipo: PDF, JPG, JPEG, PNG, DOC o DOCX.',
            'archivo.max' => 'El archivo no puede exceder 30MB.',

            'publicacion_inicio.required' => 'La fecha de inicio de publicación es obligatoria.',
            
            // 'publicacion_fin' messages removed

            'registro_inicio.required' => 'La fecha de inicio de registro es obligatoria.',
            'registro_inicio.after_or_equal' => 'El registro no puede iniciar antes de la publicación.',

            'registro_fin.required' => 'La fecha de fin de registro es obligatoria.',
            'registro_fin.after_or_equal' => 'El fin de registro debe ser posterior o igual al inicio.',

            'evaluacion_inicio.required' => 'La fecha de inicio de evaluación es obligatoria.',
            'evaluacion_inicio.after_or_equal' => 'La evaluación no puede iniciar antes de terminar el registro.',

            'evaluacion_fin.required' => 'La fecha de fin de evaluación es obligatoria.',
            'evaluacion_fin.after_or_equal' => 'El fin de evaluación debe ser posterior o igual al inicio.',

            'resultados_inicio.required' => 'La fecha de publicación de resultados es obligatoria.',
            'resultados_inicio.after_or_equal' => 'Los resultados no pueden publicarse antes de terminar la evaluación.',

            'resultados_fin.required' => 'La fecha límite de resultados es obligatoria.',
            'resultados_fin.after_or_equal' => 'El fin de resultados debe ser posterior o igual al inicio.',
        ];
    }
}
