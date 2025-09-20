<?php

namespace App\Domain\Services;

use App\Domain\Entities\Usuario;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Password;

interface UsuarioServiceInterface
{
    public function createUsuario(
        string $nombre, 
        string $email, 
        string $password,
        ?string $apellidoPaterno = null,
        ?string $apellidoMaterno = null,
        array $roles = []
    ): Usuario;
    
    public function updateUsuario(int $id, array $data): Usuario;
    
    public function activateUsuario(int $id): Usuario;
    
    public function deactivateUsuario(int $id): Usuario;
    
    public function changeUserPassword(int $id, string $newPassword): Usuario;
    
    public function assignRoleToUser(int $userId, array $role): Usuario;
    
    public function removeRoleFromUser(int $userId, string $roleName): Usuario;
    
    public function validateUniqueEmail(Email $email, ?int $excludeUserId = null): void;
}