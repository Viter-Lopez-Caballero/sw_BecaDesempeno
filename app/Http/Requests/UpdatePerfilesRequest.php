<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UpdatePerfilesRequest extends FormRequest
{
    protected string $tableName = 'roles';
    protected string $param = 'perfiles';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => "required|string|max:50|unique:roles,name" . (isset($this->route($this->param)->id) ? ',' . $this->route($this->param)->id : ''),
            'guard_name' => 'required',
            'description' => 'required',
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