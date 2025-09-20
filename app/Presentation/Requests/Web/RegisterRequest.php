<?php

namespace App\Presentation\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'nombre' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/' // Only letters, accents, and spaces
            ],
            'apellido_paterno' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'apellido_materno' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'confirmed'
            ],
            'password_confirmation' => [
                'required',
                'string'
            ]
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            
            'apellido_paterno.string' => 'El apellido paterno debe ser una cadena de texto.',
            'apellido_paterno.max' => 'El apellido paterno no puede tener más de 255 caracteres.',
            'apellido_paterno.regex' => 'El apellido paterno solo puede contener letras y espacios.',
            
            'apellido_materno.string' => 'El apellido materno debe ser una cadena de texto.',
            'apellido_materno.max' => 'El apellido materno no puede tener más de 255 caracteres.',
            'apellido_materno.regex' => 'El apellido materno solo puede contener letras y espacios.',
            
            'email.required' => 'El campo email es obligatorio.',
            'email.email' => 'El email debe tener un formato válido.',
            'email.max' => 'El email no puede tener más de 255 caracteres.',
            'email.unique' => 'Este email ya está registrado en el sistema.',
            
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña no puede tener más de 255 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            
            'password_confirmation.required' => 'La confirmación de contraseña es obligatoria.',
            'password_confirmation.string' => 'La confirmación de contraseña debe ser una cadena de texto.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'apellido_paterno' => 'apellido paterno',
            'apellido_materno' => 'apellido materno',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'password_confirmation' => 'confirmación de contraseña',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // For web requests, we want to redirect back with errors
        // This is the default behavior, but we can customize it here if needed
        parent::failedValidation($validator);
    }

    /**
     * Get the validated nombre.
     */
    public function getNombre(): string
    {
        return $this->validated('nombre');
    }

    /**
     * Get the validated apellido paterno.
     */
    public function getApellidoPaterno(): ?string
    {
        return $this->validated('apellido_paterno');
    }

    /**
     * Get the validated apellido materno.
     */
    public function getApellidoMaterno(): ?string
    {
        return $this->validated('apellido_materno');
    }

    /**
     * Get the validated email.
     */
    public function getEmail(): string
    {
        return $this->validated('email');
    }

    /**
     * Get the validated password.
     */
    public function getPassword(): string
    {
        return $this->validated('password');
    }

    /**
     * Get all validated data for user creation.
     *
     * @return array<string, mixed>
     */
    public function getValidatedUserData(): array
    {
        return [
            'nombre' => $this->getNombre(),
            'apellido_paterno' => $this->getApellidoPaterno(),
            'apellido_materno' => $this->getApellidoMaterno(),
            'email' => $this->getEmail(),
            'password' => $this->getPassword(),
        ];
    }
}