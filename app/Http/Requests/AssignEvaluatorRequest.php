<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignEvaluatorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'application_id' => 'required|exists:applications,id',
            'evaluator_ids' => 'required|array',
            'evaluator_ids.*' => 'exists:users,id',
        ];
    }
    
    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'application_id.required' => 'El ID de la solicitud es obligatorio.',
            'application_id.exists' => 'La solicitud seleccionada no existe.',
            'evaluator_ids.required' => 'Debes seleccionar al menos un evaluador.',
            'evaluator_ids.*.exists' => 'Uno de los evaluadores seleccionados no es válido.',
        ];
    }
}
