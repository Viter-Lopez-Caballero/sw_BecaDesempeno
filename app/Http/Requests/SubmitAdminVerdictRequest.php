<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAdminVerdictRequest extends FormRequest
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
            'comentario' => 'required_if:status,rejected|nullable|string|max:1000',
        ];
    }
    
    public function messages(): array
    {
        return [
            'status.required' => 'El estatus del veredicto es obligatorio.',
            'status.in' => 'El estatus seleccionado es inválido.',
            'comentario.required_if' => 'El comentario es requerido si se rechaza la solicitud.',
            'comentario.max' => 'El comentario no puede exceder los 1000 caracteres.',
        ];
    }
}
