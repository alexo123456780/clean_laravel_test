<?php

namespace App\Application\UseCases;

use App\Application\DTOs\UsuarioResponse;
use App\Domain\Repositories\UsuarioRepositoryInterface;
use App\Domain\Exceptions\UsuarioNotFoundException;

class GetUsuarioUseCase
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository
    ) {}
    
    public function execute(int $id): UsuarioResponse
    {
        $usuario = $this->usuarioRepository->findById($id);
        
        if (!$usuario) {
            throw new UsuarioNotFoundException($id);
        }
        
        return UsuarioResponse::fromEntity($usuario);
    }
}