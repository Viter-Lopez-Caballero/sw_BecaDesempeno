<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluationVerdictRequest extends FormRequest
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
            'status' => 'required|in:approved,rejected',
            'score' => 'required|numeric|min:0',
            'answers' => 'required|array',
            'comment' => 'required_if:status,rejected|nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'El veredicto es obligatorio.',
            'status.in' => 'Veredicto inválido (debe ser aprobado o rechazado).',
            'score.required' => 'La puntuación de la rúbrica es obligatoria.',
            'answers.required' => 'Debe responder las preguntas de la rúbrica.',
            'comment.required_if' => 'Debe proporcionar una retroalimentación al rechazar.',
            'comment.max' => 'La retroalimentación no puede exceder los 1000 caracteres.',
        ];
    }
}
