<?php

namespace Tests\Unit\Presentation\Http\Controllers\Web;

use Tests\TestCase;
use App\Presentation\Http\Controllers\Web\AuthController;
use App\Application\Services\AuthenticationService;
use App\Application\UseCases\CreateUsuarioUseCase;
use App\Application\DTOs\UsuarioResponse;
use App\Application\DTOs\CreateUsuarioResponse;
use App\Presentation\Requests\Web\LoginRequest;
use App\Presentation\Requests\Web\RegisterRequest;
use App\Domain\Exceptions\UsuarioNotFoundException;
use App\Domain\Exceptions\DuplicateEmailException;
use App\Domain\Exceptions\InvalidUsuarioDataException;
use App\Infrastructure\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mockery;

class AuthControllerTest extends TestCase
{
    private AuthController $controller;
    private AuthenticationService $mockAuthService;
    private CreateUsuarioUseCase $mockCreateUsuarioUseCase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockAuthService = Mockery::mock(AuthenticationService::class);
        $this->mockCreateUsuarioUseCase = Mockery::mock(CreateUsuarioUseCase::class);
        $this->controller = new AuthController(
            $this->mockAuthService,
            $this->mockCreateUsuarioUseCase
        );
    }

    public function test_show_login_form_returns_view()
    {
        // This test will be implemented when views are created
        $this->markTestSkipped('Views not yet implemented');
    }

    public function test_show_register_form_returns_view()
    {
        // This test will be implemented when views are created
        $this->markTestSkipped('Views not yet implemented');
    }

    public function test_login_with_valid_credentials_calls_authentication_service()
    {
        // Arrange
        $email = 'test@example.com';
        $password = 'password123';
        
        $usuarioResponse = new UsuarioResponse(
            id: 1,
            nombre: 'Juan',
            apellidoPaterno: 'Pérez',
            apellidoMaterno: 'García',
            email: $email,
            fullName: 'Juan Pérez García',
            roles: [],
            activo: true,
            createdAt: '2023-01-01 00:00:00',
            updatedAt: '2023-01-01 00:00:00'
        );

        $mockRequest = Mockery::mock(LoginRequest::class);
        $mockRequest->shouldReceive('getEmail')->andReturn($email);
        $mockRequest->shouldReceive('getPassword')->andReturn($password);
        $mockRequest->shouldReceive('only')->with('email')->andReturn(['email' => $email]);

        $this->mockAuthService
            ->shouldReceive('authenticate')
            ->with($email, $password)
            ->once()
            ->andReturn($usuarioResponse);

        // Act & Assert - We expect the authentication service to be called
        $this->controller->login($mockRequest);
        
        // The test passes if no exceptions are thrown and mocks are satisfied
        $this->assertTrue(true);
    }

    public function test_login_with_invalid_credentials_returns_error()
    {
        // Arrange
        $email = 'test@example.com';
        $password = 'wrongpassword';

        $mockRequest = Mockery::mock(LoginRequest::class);
        $mockRequest->shouldReceive('getEmail')->andReturn($email);
        $mockRequest->shouldReceive('getPassword')->andReturn($password);
        $mockRequest->shouldReceive('only')->with('email')->andReturn(['email' => $email]);

        $this->mockAuthService
            ->shouldReceive('authenticate')
            ->with($email, $password)
            ->once()
            ->andReturn(null);

        // Act
        $response = $this->controller->login($mockRequest);

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_login_with_non_existent_user_returns_error()
    {
        // Arrange
        $email = 'nonexistent@example.com';
        $password = 'password123';

        $mockRequest = Mockery::mock(LoginRequest::class);
        $mockRequest->shouldReceive('getEmail')->andReturn($email);
        $mockRequest->shouldReceive('getPassword')->andReturn($password);
        $mockRequest->shouldReceive('only')->with('email')->andReturn(['email' => $email]);

        $this->mockAuthService
            ->shouldReceive('authenticate')
            ->with($email, $password)
            ->once()
            ->andThrow(new UsuarioNotFoundException(0));

        // Act
        $response = $this->controller->login($mockRequest);

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_register_with_valid_data_calls_create_usuario_use_case()
    {
        // Arrange
        $userData = [
            'nombre' => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'García',
            'email' => 'juan@example.com',
            'password' => 'password123'
        ];

        $createUsuarioResponse = new CreateUsuarioResponse(
            id: 1,
            nombre: $userData['nombre'],
            email: $userData['email'],
            apellidoPaterno: $userData['apellido_paterno'],
            apellidoMaterno: $userData['apellido_materno'],
            fullName: 'Juan Pérez García',
            roles: [],
            activo: true,
            createdAt: '2023-01-01 00:00:00'
        );

        $mockRequest = Mockery::mock(RegisterRequest::class);
        $mockRequest->shouldReceive('getNombre')->andReturn($userData['nombre']);
        $mockRequest->shouldReceive('getApellidoPaterno')->andReturn($userData['apellido_paterno']);
        $mockRequest->shouldReceive('getApellidoMaterno')->andReturn($userData['apellido_materno']);
        $mockRequest->shouldReceive('getEmail')->andReturn($userData['email']);
        $mockRequest->shouldReceive('getPassword')->andReturn($userData['password']);
        $mockRequest->shouldReceive('except')->with('password', 'password_confirmation')->andReturn([
            'nombre' => $userData['nombre'],
            'email' => $userData['email']
        ]);

        $this->mockCreateUsuarioUseCase
            ->shouldReceive('execute')
            ->once()
            ->andReturn($createUsuarioResponse);

        // Act & Assert - We expect the use case to be called
        $this->controller->register($mockRequest);
        
        // The test passes if no exceptions are thrown and mocks are satisfied
        $this->assertTrue(true);
    }

    public function test_register_with_duplicate_email_returns_error()
    {
        // Arrange
        $email = 'existing@example.com';

        $mockRequest = Mockery::mock(RegisterRequest::class);
        $mockRequest->shouldReceive('getNombre')->andReturn('Juan');
        $mockRequest->shouldReceive('getApellidoPaterno')->andReturn('Pérez');
        $mockRequest->shouldReceive('getApellidoMaterno')->andReturn('García');
        $mockRequest->shouldReceive('getEmail')->andReturn($email);
        $mockRequest->shouldReceive('getPassword')->andReturn('password123');
        $mockRequest->shouldReceive('except')->with('password', 'password_confirmation')->andReturn([
            'nombre' => 'Juan',
            'email' => $email
        ]);

        $this->mockCreateUsuarioUseCase
            ->shouldReceive('execute')
            ->once()
            ->andThrow(new DuplicateEmailException($email));

        // Act
        $response = $this->controller->register($mockRequest);

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_register_with_invalid_data_returns_validation_errors()
    {
        // Arrange
        $mockRequest = Mockery::mock(RegisterRequest::class);
        $mockRequest->shouldReceive('getNombre')->andReturn('');
        $mockRequest->shouldReceive('getApellidoPaterno')->andReturn('Pérez');
        $mockRequest->shouldReceive('getApellidoMaterno')->andReturn('García');
        $mockRequest->shouldReceive('getEmail')->andReturn('test@example.com');
        $mockRequest->shouldReceive('getPassword')->andReturn('password123');
        $mockRequest->shouldReceive('except')->with('password', 'password_confirmation')->andReturn([
            'email' => 'test@example.com'
        ]);

        $this->mockCreateUsuarioUseCase
            ->shouldReceive('execute')
            ->once()
            ->andThrow(InvalidUsuarioDataException::emptyName());

        // Act
        $response = $this->controller->register($mockRequest);

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_logout_calls_auth_logout()
    {
        // This test will be implemented when routes are created
        $this->markTestSkipped('Routes not yet implemented');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}