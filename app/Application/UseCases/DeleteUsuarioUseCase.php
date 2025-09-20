<?php

namespace App\Application\UseCases;

use App\Domain\Services\UsuarioServiceInterface;

class DeleteUsuarioUseCase
{
    public function __construct(
        private UsuarioServiceInterface $usuarioService
    ) {}
    
    public function execute(int $id): bool
    {
        // Implementamos soft delete desactivando el usuario
        $this->usuarioService->deactivateUsuario($id);
        
        return true;
    }
}