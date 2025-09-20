<?php

namespace Tests\Unit\Presentation\Requests\Web;

use Tests\TestCase;
use App\Presentation\Requests\Web\LoginRequest;
use Illuminate\Support\Facades\Validator;

class LoginRequestTest extends TestCase
{
    private LoginRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new LoginRequest();
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
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        // Act
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        // Assert
        $this->assertFalse($validator->fails());
        $this->assertEmpty($validator->errors()->all());
    }

    public function test_validation_fails_when_email_is_missing()
    {
        // Arrange
        $data = [
            'password' => 'password123'
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
            'email' => 'invalid-email',
            'password' => 'password123'
        ];

        // Act
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertContains('El email debe tener un formato válido.', $validator->errors()->get('email'));
    }

    public function test_validation_fails_when_email_is_too_long()
    {
        // Arrange
        $longEmail = str_repeat('a', 250) . '@example.com'; // 261 characters
        $data = [
            'email' => $longEmail,
            'password' => 'password123'
        ];

        // Act
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertContains('El email no puede tener más de 255 caracteres.', $validator->errors()->get('email'));
    }

    public function test_validation_fails_when_password_is_missing()
    {
        // Arrange
        $data = [
            'email' => 'test@example.com'
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
            'email' => 'test@example.com',
            'password' => '1234567' // 7 characters
        ];

        // Act
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
        $this->assertContains('La contraseña debe tener al menos 8 caracteres.', $validator->errors()->get('password'));
    }

    public function test_validation_fails_when_password_is_too_long()
    {
        // Arrange
        $longPassword = str_repeat('a', 256); // 256 characters
        $data = [
            'email' => 'test@example.com',
            'password' => $longPassword
        ];

        // Act
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
        $this->assertContains('La contraseña no puede tener más de 255 caracteres.', $validator->errors()->get('password'));
    }

    public function test_validation_fails_when_password_is_not_string()
    {
        // Arrange
        $data = [
            'email' => 'test@example.com',
            'password' => 12345678 // numeric instead of string
        ];

        // Act
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
        $this->assertContains('La contraseña debe ser una cadena de texto.', $validator->errors()->get('password'));
    }

    public function test_get_email_returns_validated_email()
    {
        // Arrange
        $email = 'test@example.com';
        $this->request->merge(['email' => $email, 'password' => 'password123']);
        $this->request->setValidator(
            Validator::make($this->request->all(), $this->request->rules())
        );

        // Act
        $result = $this->request->getEmail();

        // Assert
        $this->assertEquals($email, $result);
    }

    public function test_get_password_returns_validated_password()
    {
        // Arrange
        $password = 'password123';
        $this->request->merge(['email' => 'test@example.com', 'password' => $password]);
        $this->request->setValidator(
            Validator::make($this->request->all(), $this->request->rules())
        );

        // Act
        $result = $this->request->getPassword();

        // Assert
        $this->assertEquals($password, $result);
    }

    public function test_attributes_returns_correct_spanish_names()
    {
        // Act
        $attributes = $this->request->attributes();

        // Assert
        $this->assertEquals('correo electrónico', $attributes['email']);
        $this->assertEquals('contraseña', $attributes['password']);
    }

    public function test_rules_returns_expected_validation_rules()
    {
        // Act
        $rules = $this->request->rules();

        // Assert
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);
        
        $this->assertContains('required', $rules['email']);
        $this->assertContains('email', $rules['email']);
        $this->assertContains('max:255', $rules['email']);
        
        $this->assertContains('required', $rules['password']);
        $this->assertContains('string', $rules['password']);
        $this->assertContains('min:8', $rules['password']);
        $this->assertContains('max:255', $rules['password']);
    }
}