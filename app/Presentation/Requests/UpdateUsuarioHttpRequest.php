<?php

namespace App\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioHttpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('usuario');
        
        return [
            'nombre' => ['sometimes', 'string', 'max:255'],
            'apellido_paterno' => ['sometimes', 'nullable', 'string', 'max:255'],
            'apellido_materno' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => [
                'sometimes', 
                'email', 
                'max:255', 
                Rule::unique('users', 'email')->ignore($userId)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'email.email' => 'El email debe tener un formato válido',
            'email.unique' => 'Este email ya está en uso',
            'apellido_paterno.max' => 'El apellido paterno no puede exceder 255 caracteres',
            'apellido_materno.max' => 'El apellido materno no puede exceder 255 caracteres',
        ];
    }
}