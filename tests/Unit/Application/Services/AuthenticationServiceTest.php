<?php

namespace Tests\Unit\Application\Services;

use Tests\TestCase;
use App\Application\Services\AuthenticationService;
use App\Application\UseCases\GetUsuarioUseCase;
use App\Application\UseCases\GetUsuarioByEmailUseCase;
use App\Application\DTOs\UsuarioResponse;
use App\Domain\Exceptions\UsuarioNotFoundException;
use App\Domain\Repositories\UsuarioRepositoryInterface;
use App\Domain\Entities\Usuario;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Password;
use App\Infrastructure\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class AuthenticationServiceTest extends TestCase
{

    private AuthenticationService $authenticationService;
    private GetUsuarioUseCase $mockGetUsuarioUseCase;
    private GetUsuarioByEmailUseCase $mockGetUsuarioByEmailUseCase;
    private UsuarioRepositoryInterface $mockUsuarioRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockGetUsuarioUseCase = Mockery::mock(GetUsuarioUseCase::class);
        $this->mockGetUsuarioByEmailUseCase = Mockery::mock(GetUsuarioByEmailUseCase::class);
        $this->mockUsuarioRepository = Mockery::mock(UsuarioRepositoryInterface::class);
        $this->authenticationService = new AuthenticationService(
            $this->mockGetUsuarioUseCase,
            $this->mockGetUsuarioByEmailUseCase,
            $this->mockUsuarioRepository
        );
    }

    public function test_authenticate_with_valid_credentials_returns_usuario_response()
    {
        // Arrange
        $email = 'test@example.com';
        $password = 'password123';
        $emailVO = new Email($email);
        
        $usuario = new Usuario(
            nombre: 'Juan',
            email: $emailVO,
            password: Password::fromPlainText($password),
            apellidoPaterno: 'Pérez',
            apellidoMaterno: 'García',
            activo: true,
            roles: [],
            id: 1,
            createdAt: Carbon::now(),
            updatedAt: Carbon::now()
        );

        $this->mockUsuarioRepository
            ->shouldReceive('findByEmail')
            ->with(Mockery::on(function ($arg) use ($email) {
                return $arg instanceof Email && $arg->getValue() === $email;
            }))
            ->once()
            ->andReturn($usuario);

        // Act
        $result = $this->authenticationService->authenticate($email, $password);

        // Assert
        $this->assertInstanceOf(UsuarioResponse::class, $result);
        $this->assertEquals($email, $result->email);
        $this->assertEquals('Juan', $result->nombre);
    }

    public function test_authenticate_with_invalid_password_returns_null()
    {
        // Arrange
        $email = 'test@example.com';
        $correctPassword = 'password123';
        $wrongPassword = 'wrongpassword';
        $emailVO = new Email($email);
        
        $usuario = new Usuario(
            nombre: 'Juan',
            email: $emailVO,
            password: Password::fromPlainText($correctPassword),
            apellidoPaterno: 'Pérez',
            apellidoMaterno: 'García',
            activo: true,
            roles: [],
            id: 1,
            createdAt: Carbon::now(),
            updatedAt: Carbon::now()
        );

        $this->mockUsuarioRepository
            ->shouldReceive('findByEmail')
            ->with(Mockery::on(function ($arg) use ($email) {
                return $arg instanceof Email && $arg->getValue() === $email;
            }))
            ->once()
            ->andReturn($usuario);

        // Act
        $result = $this->authenticationService->authenticate($email, $wrongPassword);

        // Assert
        $this->assertNull($result);
    }

    public function test_authenticate_with_non_existent_user_throws_exception()
    {
        // Arrange
        $email = 'nonexistent@example.com';
        $password = 'password123';

        $this->mockUsuarioRepository
            ->shouldReceive('findByEmail')
            ->with(Mockery::on(function ($arg) use ($email) {
                return $arg instanceof Email && $arg->getValue() === $email;
            }))
            ->once()
            ->andReturn(null);

        // Act & Assert
        $this->expectException(UsuarioNotFoundException::class);
        $this->authenticationService->authenticate($email, $password);
    }

    public function test_get_authenticated_user_returns_usuario_response()
    {
        // Arrange
        $userId = 1;
        $usuarioResponse = new UsuarioResponse(
            id: $userId,
            nombre: 'Juan',
            apellidoPaterno: 'Pérez',
            apellidoMaterno: 'García',
            email: 'test@example.com',
            fullName: 'Juan Pérez García',
            roles: [],
            activo: true,
            createdAt: '2023-01-01 00:00:00',
            updatedAt: '2023-01-01 00:00:00'
        );

        $this->mockGetUsuarioUseCase
            ->shouldReceive('execute')
            ->with($userId)
            ->once()
            ->andReturn($usuarioResponse);

        // Act
        $result = $this->authenticationService->getAuthenticatedUser($userId);

        // Assert
        $this->assertInstanceOf(UsuarioResponse::class, $result);
        $this->assertEquals($userId, $result->id);
    }

    public function test_get_authenticated_user_with_invalid_id_returns_null()
    {
        // Arrange
        $userId = 999;

        $this->mockGetUsuarioUseCase
            ->shouldReceive('execute')
            ->with($userId)
            ->once()
            ->andThrow(UsuarioNotFoundException::byEmail('test@example.com'));

        // Act
        $result = $this->authenticationService->getAuthenticatedUser($userId);

        // Assert
        $this->assertNull($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}