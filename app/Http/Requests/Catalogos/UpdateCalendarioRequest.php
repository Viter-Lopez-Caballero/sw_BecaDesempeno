<?php

namespace App\Http\Requests\Catalogos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCalendarioRequest extends FormRequest
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
            'convocatoria_id' => ['required', 'exists:convocatorias,id'],
            'publicacion_inicio' => ['required', 'date'],
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
            'convocatoria_id.required' => 'La convocatoria es obligatoria.',
            'convocatoria_id.exists' => 'La convocatoria seleccionada no existe.',
            'publicacion_inicio.required' => 'La fecha de inicio de publicación es obligatoria.',
            'registro_inicio.required' => 'La fecha de inicio de registro es obligatoria.',
            'registro_inicio.after_or_equal' => 'La fecha de inicio de registro debe ser igual o posterior al inicio de publicación.',
            'registro_fin.required' => 'La fecha de fin de registro es obligatoria.',
            'registro_fin.after_or_equal' => 'La fecha de fin de registro debe ser igual o posterior a la fecha de inicio.',
            'evaluacion_inicio.required' => 'La fecha de inicio de evaluación es obligatoria.',
            'evaluacion_inicio.after_or_equal' => 'La fecha de inicio de evaluación debe ser igual o posterior al fin de registro.',
            'evaluacion_fin.required' => 'La fecha de fin de evaluación es obligatoria.',
            'evaluacion_fin.after_or_equal' => 'La fecha de fin de evaluación debe ser igual o posterior a la fecha de inicio.',
            'resultados_inicio.required' => 'La fecha de inicio de resultados es obligatoria.',
            'resultados_inicio.after_or_equal' => 'La fecha de inicio de resultados debe ser igual o posterior al fin de evaluación.',
            'resultados_fin.required' => 'La fecha de fin de resultados es obligatoria.',
            'resultados_fin.after_or_equal' => 'La fecha de fin de resultados debe ser igual o posterior a la fecha de inicio.',
        ];
    }
}
