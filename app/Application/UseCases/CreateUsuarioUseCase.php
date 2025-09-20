<?php

namespace App\Application\UseCases;

use App\Application\DTOs\CreateUsuarioRequest;
use App\Application\DTOs\CreateUsuarioResponse;
use App\Domain\Services\UsuarioServiceInterface;

class CreateUsuarioUseCase
{
    public function __construct(
        private UsuarioServiceInterface $usuarioService
    ) {}
    
    public function execute(CreateUsuarioRequest $request): CreateUsuarioResponse
    {
        $usuario = $this->usuarioService->createUsuario(
            nombre: $request->nombre,
            email: $request->email,
            password: $request->password,
            apellidoPaterno: $request->apellidoPaterno,
            apellidoMaterno: $request->apellidoMaterno,
            roles: $request->roles
        );
        
        return CreateUsuarioResponse::fromEntity($usuario);
    }
}