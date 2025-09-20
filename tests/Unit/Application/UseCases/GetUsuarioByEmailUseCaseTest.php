<?php

namespace Tests\Unit\Application\UseCases;

use Tests\TestCase;
use App\Application\UseCases\GetUsuarioByEmailUseCase;
use App\Application\DTOs\UsuarioResponse;
use App\Domain\Repositories\UsuarioRepositoryInterface;
use App\Domain\Entities\Usuario;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Password;
use App\Domain\Exceptions\UsuarioNotFoundException;
use Carbon\Carbon;
use Mockery;

class GetUsuarioByEmailUseCaseTest extends TestCase
{
    private GetUsuarioByEmailUseCase $useCase;
    private UsuarioRepositoryInterface $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockRepository = Mockery::mock(UsuarioRepositoryInterface::class);
        $this->useCase = new GetUsuarioByEmailUseCase($this->mockRepository);
    }

    public function test_execute_with_existing_email_returns_usuario_response()
    {
        // Arrange
        $email = 'test@example.com';
        $emailVO = new Email($email);
        
        $usuario = new Usuario(
            nombre: 'Juan',
            email: $emailVO,
            password: Password::fromPlainText('password123'),
            apellidoPaterno: 'Pérez',
            apellidoMaterno: 'García',
            activo: true,
            roles: [],
            id: 1,
            createdAt: Carbon::now(),
            updatedAt: Carbon::now()
        );

        $this->mockRepository
            ->shouldReceive('findByEmail')
            ->with(Mockery::on(function ($arg) use ($email) {
                return $arg instanceof Email && $arg->getValue() === $email;
            }))
            ->once()
            ->andReturn($usuario);

        // Act
        $result = $this->useCase->execute($email);

        // Assert
        $this->assertInstanceOf(UsuarioResponse::class, $result);
        $this->assertEquals($email, $result->email);
        $this->assertEquals('Juan', $result->nombre);
        $this->assertEquals(1, $result->id);
    }

    public function test_execute_with_non_existent_email_throws_exception()
    {
        // Arrange
        $email = 'nonexistent@example.com';
        $emailVO = new Email($email);

        $this->mockRepository
            ->shouldReceive('findByEmail')
            ->with(Mockery::on(function ($arg) use ($email) {
                return $arg instanceof Email && $arg->getValue() === $email;
            }))
            ->once()
            ->andReturn(null);

        // Act & Assert
        $this->expectException(UsuarioNotFoundException::class);
        $this->useCase->execute($email);
    }

    public function test_execute_with_invalid_email_throws_exception()
    {
        // Arrange
        $invalidEmail = 'invalid-email';

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->useCase->execute($invalidEmail);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}