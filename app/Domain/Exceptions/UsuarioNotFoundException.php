<?php

namespace App\Domain\Exceptions;

class UsuarioNotFoundException extends DomainException
{
    protected string $errorType = 'usuario_not_found';
    
    public function __construct(int $id)
    {
        parent::__construct("Usuario con ID {$id} no encontrado", 404);
    }
    
    public static function byEmail(string $email): self
    {
        $exception = new self(0);
        $exception->message = "Usuario con email {$email} no encontrado";
        return $exception;
    }
}