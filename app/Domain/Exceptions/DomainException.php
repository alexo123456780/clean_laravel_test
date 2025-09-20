<?php

namespace App\Domain\Exceptions;

use Exception;

abstract class DomainException extends Exception
{
    protected string $errorType;
    
    public function __construct(string $message = "", int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
    public function getErrorType(): string
    {
        return $this->errorType;
    }
}