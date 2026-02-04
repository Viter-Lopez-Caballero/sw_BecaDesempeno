<?php

namespace App\Http\Requests\Security;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'              => 'required|max:255',
            'email'             => 'required|email|max:255|unique:users,email,' . $this->id,
            'password'          => 'nullable|max:20',
            'roles'             => 'required|array',
            'roles.*'           => 'exists:roles,id',
        ];
    }
    public function attributes(): array
    {
        return [
            'name'              => 'nombre',
            'email'             => 'correo electrónico',
            'password'          => 'contraseña',
            'roles'             => 'roles',
            'roles.*'           => 'rol',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         => 'El nombre completo es obligatorio',
            'name.max'              => 'El nombre no puede exceder 255 caracteres',
            'email.required'        => 'El correo electrónico es obligatorio',
            'email.email'           => 'El correo electrónico debe ser válido',
            'email.max'             => 'El correo electrónico no puede exceder 255 caracteres',
            'email.unique'          => 'Este correo electrónico ya está registrado',
            'password.max'          => 'La contraseña no puede exceder 20 caracteres',
            'roles.required'        => 'Debes asignar al menos un rol',
            'roles.array'           => 'Los roles deben ser un arreglo',
            'roles.*.exists'        => 'Uno de los roles seleccionados no existe',
        ];
    }
}
