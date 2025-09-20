<?php

namespace App\Application\Services;

use App\Application\UseCases\GetUsuarioUseCase;
use App\Application\UseCases\GetUsuarioByEmailUseCase;
use App\Application\DTOs\UsuarioResponse;
use App\Domain\Exceptions\DomainException;
use App\Domain\Exceptions\UsuarioNotFoundException;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Password;
use App\Domain\Repositories\UsuarioRepositoryInterface;
use App\Infrastructure\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthenticationService
{
    public function __construct(
        private GetUsuarioUseCase $getUsuarioUseCase,
        private GetUsuarioByEmailUseCase $getUsuarioByEmailUseCase,
        private UsuarioRepositoryInterface $usuarioRepository
    ) {}

    /**
     * Authenticate user with email and password
     * 
     * @param string $email
     * @param string $password
     * @return UsuarioResponse|null
     * @throws DomainException
     */
    public function authenticate(string $email, string $password): ?UsuarioResponse
    {
        try {
            // Get the Usuario entity directly from repository
            $emailVO = new Email($email);
            $usuario = $this->usuarioRepository->findByEmail($emailVO);
            
            if (!$usuario) {
                throw UsuarioNotFoundException::byEmail($email);
            }
            
            // Verify password using domain Password value object
            if (!$usuario->getPassword()->verify($password)) {
                return null;
            }
            
            // Convert to response DTO
            return UsuarioResponse::fromEntity($usuario);
        } catch (DomainException $e) {
            // Re-throw domain exceptions to be handled by controller
            throw $e;
        }
    }

    /**
     * Convert UsuarioResponse to Laravel User model for authentication
     * 
     * @param UsuarioResponse $usuarioResponse
     * @return User
     */
    public function convertToAuthUser(UsuarioResponse $usuarioResponse): User
    {
        // Find or create the User model for Laravel Auth
        $user = User::where('email', $usuarioResponse->email)->first();
        
        if (!$user) {
            // This should not happen in normal flow, but handle gracefully
            $user = new User([
                'nombre' => $usuarioResponse->nombre,
                'apellido_paterno' => $usuarioResponse->apellidoPaterno,
                'apellido_materno' => $usuarioResponse->apellidoMaterno,
                'email' => $usuarioResponse->email,
                'password' => $usuarioResponse->password,
                'activo' => $usuarioResponse->activo,
            ]);
            $user->id = $usuarioResponse->id;
        }
        
        return $user;
    }

    /**
     * Get authenticated user data
     * 
     * @param int $userId
     * @return UsuarioResponse|null
     */
    public function getAuthenticatedUser(int $userId): ?UsuarioResponse
    {
        try {
            return $this->getUsuarioUseCase->execute($userId);
        } catch (DomainException $e) {
            return null;
        }
    }
}