<?php

namespace App\Domain\Exceptions;

class InvalidUsuarioDataException extends DomainException
{
    protected string $errorType = 'invalid_usuario_data';
    
    public function __construct(string $message)
    {
        parent::__construct($message, 400);
    }
    
    public static function emptyName(): self
    {
        return new self('El nombre del usuario no puede estar vacío');
    }
    
    public static function invalidEmail(string $email): self
    {
        return new self("El email '{$email}' no es válido");
    }
    
    public static function weakPassword(): self
    {
        return new self('La contraseña no cumple con los requisitos de seguridad');
    }
}