<?php

namespace App\Application\DTOs;

use App\Domain\Entities\Usuario;

class CreateUsuarioResponse
{
    public function __construct(
        public readonly int $id,
        public readonly string $nombre,
        public readonly string $email,
        public readonly ?string $apellidoPaterno,
        public readonly ?string $apellidoMaterno,
        public readonly string $fullName,
        public readonly array $roles,
        public readonly bool $activo,
        public readonly string $createdAt
    ) {}
    
    public static function fromEntity(Usuario $usuario): self
    {
        return new self(
            id: $usuario->getId(),
            nombre: $usuario->getNombre(),
            email: $usuario->getEmail()->getValue(),
            apellidoPaterno: $usuario->getApellidoPaterno(),
            apellidoMaterno: $usuario->getApellidoMaterno(),
            fullName: $usuario->getFullName(),
            roles: $usuario->getRoles(),
            activo: $usuario->isActive(),
            createdAt: $usuario->getCreatedAt()->toDateTimeString()
        );
    }
}