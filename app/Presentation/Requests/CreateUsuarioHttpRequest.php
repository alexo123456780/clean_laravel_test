<?php

namespace App\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUsuarioHttpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido_paterno' => ['nullable', 'string', 'max:255'],
            'apellido_materno' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:20'],
            'roles' => ['sometimes', 'array'],
            'roles.*.name' => ['required_with:roles', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe tener un formato válido',
            'email.unique' => 'Este email ya está en uso',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.max' => 'La contraseña no puede exceder 20 caracteres',
            'apellido_paterno.max' => 'El apellido paterno no puede exceder 255 caracteres',
            'apellido_materno.max' => 'El apellido materno no puede exceder 255 caracteres',
        ];
    }
}