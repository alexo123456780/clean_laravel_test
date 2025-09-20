<?php

namespace App\Application\UseCases;

use App\Application\DTOs\UsuarioResponse;
use App\Domain\Repositories\UsuarioRepositoryInterface;
use App\Domain\Exceptions\UsuarioNotFoundException;
use App\Domain\ValueObjects\Email;

class GetUsuarioByEmailUseCase
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository
    ) {}
    
    public function execute(string $email): UsuarioResponse
    {
        $emailVO = new Email($email);
        $usuario = $this->usuarioRepository->findByEmail($emailVO);
        
        if (!$usuario) {
            throw UsuarioNotFoundException::byEmail($email);
        }
        
        return UsuarioResponse::fromEntity($usuario);
    }
}