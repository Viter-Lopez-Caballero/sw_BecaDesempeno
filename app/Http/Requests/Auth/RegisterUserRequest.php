<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rules;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Any guest can register
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'curp' => 'required|string|size:18',
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'institution_id' => 'required|exists:institutions,id',
            'priority_area_id' => 'required|exists:priority_areas,id',
            'sub_area_id' => 'required|exists:sub_areas,id',
            'role_type' => 'sometimes|string|in:evaluador,docente', // Optional parameter to determine role
        ];
    }
}
