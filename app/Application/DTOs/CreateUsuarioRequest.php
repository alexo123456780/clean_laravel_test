<?php

namespace App\Application\DTOs;

class CreateUsuarioRequest
{
    public function __construct(
        public readonly string $nombre,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $apellidoPaterno = null,
        public readonly ?string $apellidoMaterno = null,
        public readonly array $roles = []
    ) {}
}