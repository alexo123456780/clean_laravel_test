<?php

namespace App\Domain\Services;

use App\Domain\Entities\Usuario;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Password;
use App\Domain\Repositories\UsuarioRepositoryInterface;
use App\Domain\Exceptions\UsuarioNotFoundException;
use App\Domain\Exceptions\DuplicateEmailException;
use App\Domain\Exceptions\InvalidUsuarioDataException;

class UsuarioService implements UsuarioServiceInterface
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository
    ) {}

    public function createUsuario(
        string $nombre, 
        string $email, 
        string $password,
        ?string $apellidoPaterno = null,
        ?string $apellidoMaterno = null,
        array $roles = []
    ): Usuario {
        // Validar email único
        $emailVO = new Email($email);
        $this->validateUniqueEmail($emailVO);
        
        // Crear password
        $passwordVO = Password::fromPlainText($password);
        
        // Crear usuario
        $usuario = new Usuario(
            nombre: $nombre,
            email: $emailVO,
            password: $passwordVO,
            apellidoPaterno: $apellidoPaterno,
            apellidoMaterno: $apellidoMaterno,
            roles: $roles
        );
        
        return $this->usuarioRepository->save($usuario);
    }
    
    public function updateUsuario(int $id, array $data): Usuario
    {
        $usuario = $this->usuarioRepository->findById($id);
        
        if (!$usuario) {
            throw new UsuarioNotFoundException($id);
        }
        
        // Validar email único si se está actualizando
        if (isset($data['email'])) {
            $newEmail = new Email($data['email']);
            if (!$usuario->getEmail()->equals($newEmail)) {
                $this->validateUniqueEmail($newEmail, $id);
            }
        }
        
        // Actualizar perfil si hay datos de nombre
        if (isset($data['nombre']) || isset($data['apellidoPaterno']) || isset($data['apellidoMaterno'])) {
            $usuario->updateProfile(
                $data['nombre'] ?? $usuario->getNombre(),
                $data['apellidoPaterno'] ?? $usuario->getApellidoPaterno(),
                $data['apellidoMaterno'] ?? $usuario->getApellidoMaterno()
            );
        }
        
        return $this->usuarioRepository->save($usuario);
    }
    
    public function activateUsuario(int $id): Usuario
    {
        $usuario = $this->usuarioRepository->findById($id);
        
        if (!$usuario) {
            throw new UsuarioNotFoundException($id);
        }
        
        $usuario->activate();
        
        return $this->usuarioRepository->save($usuario);
    }
    
    public function deactivateUsuario(int $id): Usuario
    {
        $usuario = $this->usuarioRepository->findById($id);
        
        if (!$usuario) {
            throw new UsuarioNotFoundException($id);
        }
        
        $usuario->deactivate();
        
        return $this->usuarioRepository->save($usuario);
    }
    
    public function changeUserPassword(int $id, string $newPassword): Usuario
    {
        $usuario = $this->usuarioRepository->findById($id);
        
        if (!$usuario) {
            throw new UsuarioNotFoundException($id);
        }
        
        $passwordVO = Password::fromPlainText($newPassword);
        $usuario->changePassword($passwordVO);
        
        return $this->usuarioRepository->save($usuario);
    }
    
    public function assignRoleToUser(int $userId, array $role): Usuario
    {
        $usuario = $this->usuarioRepository->findById($userId);
        
        if (!$usuario) {
            throw new UsuarioNotFoundException($userId);
        }
        
        $usuario->assignRole($role);
        
        return $this->usuarioRepository->save($usuario);
    }
    
    public function removeRoleFromUser(int $userId, string $roleName): Usuario
    {
        $usuario = $this->usuarioRepository->findById($userId);
        
        if (!$usuario) {
            throw new UsuarioNotFoundException($userId);
        }
        
        $usuario->removeRole($roleName);
        
        return $this->usuarioRepository->save($usuario);
    }
    
    public function validateUniqueEmail(Email $email, ?int $excludeUserId = null): void
    {
        $existingUser = $this->usuarioRepository->findByEmail($email);
        
        if ($existingUser && ($excludeUserId === null || $existingUser->getId() !== $excludeUserId)) {
            throw new DuplicateEmailException($email->getValue());
        }
    }
}