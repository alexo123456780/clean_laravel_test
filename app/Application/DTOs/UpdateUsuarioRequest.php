<?php

namespace App\Application\DTOs;

class UpdateUsuarioRequest
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $nombre = null,
        public readonly ?string $email = null,
        public readonly ?string $apellidoPaterno = null,
        public readonly ?string $apellidoMaterno = null
    ) {}
    
    public function toArray(): array
    {
        return array_filter([
            'nombre' => $this->nombre,
            'email' => $this->email,
            'apellidoPaterno' => $this->apellidoPaterno,
            'apellidoMaterno' => $this->apellidoMaterno,
        ], fn($value) => $value !== null);
    }
}