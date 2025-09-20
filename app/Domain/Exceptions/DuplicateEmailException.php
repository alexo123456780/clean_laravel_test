<?php

namespace App\Domain\Exceptions;

class DuplicateEmailException extends DomainException
{
    protected string $errorType = 'duplicate_email';
    
    public function __construct(string $email)
    {
        parent::__construct("El email '{$email}' ya está en uso", 409);
    }
}