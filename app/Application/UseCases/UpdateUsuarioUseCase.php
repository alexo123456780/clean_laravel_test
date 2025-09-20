<?php

namespace App\Application\UseCases;

use App\Application\DTOs\UpdateUsuarioRequest;
use App\Application\DTOs\UsuarioResponse;
use App\Domain\Services\UsuarioServiceInterface;

class UpdateUsuarioUseCase
{
    public function __construct(
        private UsuarioServiceInterface $usuarioService
    ) {}
    
    public function execute(UpdateUsuarioRequest $request): UsuarioResponse
    {
        $usuario = $this->usuarioService->updateUsuario(
            id: $request->id,
            data: $request->toArray()
        );
        
        return UsuarioResponse::fromEntity($usuario);
    }
}