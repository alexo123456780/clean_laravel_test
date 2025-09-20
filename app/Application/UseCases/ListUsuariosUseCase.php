<?php

namespace App\Application\UseCases;

use App\Application\DTOs\UsuarioResponse;
use App\Domain\Repositories\UsuarioRepositoryInterface;

class ListUsuariosUseCase
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository
    ) {}
    
    public function execute(int $page = 1, int $perPage = 15): array
    {
        $usuarios = $this->usuarioRepository->findAll($page, $perPage);
        
        return [
            'data' => array_map(
                fn($usuario) => UsuarioResponse::fromEntity($usuario),
                $usuarios
            ),
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'total' => count($usuarios)
            ]
        ];
    }
    
    public function executeActiveOnly(): array
    {
        $usuarios = $this->usuarioRepository->findActive();
        
        return array_map(
            fn($usuario) => UsuarioResponse::fromEntity($usuario),
            $usuarios
        );
    }
}