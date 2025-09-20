<?php

namespace Tests\Unit\Presentation\Requests\Web;

use Tests\TestCase;
use App\Presentation\Requests\Web\RegisterRequest;
use App\Infrastructure\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterRequestTest extends TestCase
{
    use RefreshDatabase;

    private RegisterRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new RegisterRequest();
    }

    public function test_authorize_returns_true()
    {
        // Act & Assert
        $this->assertTrue($this->request->authorize());
    }

    public function test_validation_passes_with_valid_data()
    {
        // Arrange
        $data = [
            'nombre' => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'García',
            'email' => 'juan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        // Act
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        // Assert
        $this->assertFalse($validator->fails());
        $this->assertEmpty($validator->errors()->all());
    }

    public function test_validation_passes_with_minimal_required_data()
    {
        // Arrange
        $data = [
            'nombre' => 'Juan',
            'email' => 'juan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        // Act
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        // Assert
        $this->assertFalse($validator->fails());
        $this->assertEmpty($validator->errors()->all());
    }

    public function test_validation_fails_when_nombre_is_missing()
    {
        // Arrange
        $data = [
            'email' => 'juan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        // Act
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nombre', $validator->errors()->toArray());
        $this->assertContains('El campo nombre es obligatorio.', $validator->errors()->get('nombre'));
    }

    public function test_validation_fails_when_nombre_contains_numbers()
    {
        // Arrange
        $data = [
            'nombre' => 'Juan123',
            'email' => 'juan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        // Act
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nombre', $validator->errors()->toArray());
        $this->assertContains('El nombre solo puede contener letras y espacios.', $validator->errors()->get('nombre'));
    }

    public function test_validation_passes_with_accented_characters()
    {
        // Arrange
        $data = [
            'nombre' => 'José María',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'García',
            'email' => 'jose@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        // Act
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        // Assert
        $this->assertFalse($validator->fails());
    }

    public function test_validation_fails_when_email_is_missing()
    {
        // Arrange
        $data = [
            'nombre' => 'Juan',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        // Act
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertContains('El campo email es obligatorio.', $validator->errors()->get('email'));
    }

    public function test_validation_fails_when_email_is_invalid()
    {
        // Arrange
        $data = [
            'nombre' => 'Juan',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        // Act
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertContains('El email debe tener un formato válido.', $validator->errors()->get('email'));
    }

    public function test_validation_fails_when_email_already_exists()
    {
        // Arrange
        User::factory()->create(['email' => 'existing@example.com']);
        
        $data = [
            'nombre' => 'Juan',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        // Act
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertContains('Este email ya está registrado en el sistema.', $validator->errors()->get('email'));
    }

    public function test_validation_fails_when_password_is_missing()
    {
        // Arrange
        $data = [
            'nombre' => 'Juan',
            'email' => 'juan@example.com',
            'password_confirmation' => 'password123'
        ];

        // Act
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
        $this->assertContains('El campo contraseña es obligatorio.', $validator->errors()->get('password'));
    }

    public function test_validation_fails_when_password_is_too_short()
    {
        // Arrange
        $data = [
            'nombre' => 'Juan',
            'email' => 'juan@example.com',
            'password' => '1234567', // 7 characters
            'password_confirmation' => '1234567'
        ];

        // Act
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
        $this->assertContains('La contraseña debe tener al menos 8 caracteres.', $validator->errors()->get('password'));
    }

    public function test_validation_fails_when_password_confirmation_missing()
    {
        // Arrange
        $data = [
            'nombre' => 'Juan',
            'email' => 'juan@example.com',
            'password' => 'password123'
        ];

        // Act
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password_confirmation', $validator->errors()->toArray());
        $this->assertContains('La confirmación de contraseña es obligatoria.', $validator->errors()->get('password_confirmation'));
    }

    public function test_validation_fails_when_password_confirmation_does_not_match()
    {
        // Arrange
        $data = [
            'nombre' => 'Juan',
            'email' => 'juan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password'
        ];

        // Act
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
        $this->assertContains('La confirmación de contraseña no coincide.', $validator->errors()->get('password'));
    }

    public function test_get_nombre_returns_validated_nombre()
    {
        // Arrange
        $nombre = 'Juan';
        $this->request->merge([
            'nombre' => $nombre,
            'email' => 'juan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        $this->request->setValidator(
            Validator::make($this->request->all(), $this->request->rules())
        );

        // Act
        $result = $this->request->getNombre();

        // Assert
        $this->assertEquals($nombre, $result);
    }

    public function test_get_apellido_paterno_returns_validated_apellido()
    {
        // Arrange
        $apellido = 'Pérez';
        $this->request->merge([
            'nombre' => 'Juan',
            'apellido_paterno' => $apellido,
            'email' => 'juan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        $this->request->setValidator(
            Validator::make($this->request->all(), $this->request->rules())
        );

        // Act
        $result = $this->request->getApellidoPaterno();

        // Assert
        $this->assertEquals($apellido, $result);
    }

    public function test_get_validated_user_data_returns_complete_array()
    {
        // Arrange
        $data = [
            'nombre' => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'García',
            'email' => 'juan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];
        
        $this->request->merge($data);
        $this->request->setValidator(
            Validator::make($this->request->all(), $this->request->rules())
        );

        // Act
        $result = $this->request->getValidatedUserData();

        // Assert
        $this->assertEquals('Juan', $result['nombre']);
        $this->assertEquals('Pérez', $result['apellido_paterno']);
        $this->assertEquals('García', $result['apellido_materno']);
        $this->assertEquals('juan@example.com', $result['email']);
        $this->assertEquals('password123', $result['password']);
    }

    public function test_attributes_returns_correct_spanish_names()
    {
        // Act
        $attributes = $this->request->attributes();

        // Assert
        $this->assertEquals('nombre', $attributes['nombre']);
        $this->assertEquals('apellido paterno', $attributes['apellido_paterno']);
        $this->assertEquals('apellido materno', $attributes['apellido_materno']);
        $this->assertEquals('correo electrónico', $attributes['email']);
        $this->assertEquals('contraseña', $attributes['password']);
        $this->assertEquals('confirmación de contraseña', $attributes['password_confirmation']);
    }
}