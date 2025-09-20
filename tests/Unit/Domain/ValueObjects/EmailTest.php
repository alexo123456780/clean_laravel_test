<?php

namespace Tests\Unit\Domain\ValueObjects;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObjects\Email;

class EmailTest extends TestCase
{
    public function test_can_create_valid_email()
    {
        $email = new Email('test@example.com');
        
        $this->assertEquals('test@example.com', $email->getValue());
        $this->assertEquals('example.com', $email->getDomain());
        $this->assertEquals('test', $email->getLocalPart());
    }
    
    public function test_normalizes_email_to_lowercase()
    {
        $email = new Email('TEST@EXAMPLE.COM');
        
        $this->assertEquals('test@example.com', $email->getValue());
    }
    
    public function test_throws_exception_for_empty_email()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('El email es un campo obligatorio');
        
        new Email('');
    }
    
    public function test_throws_exception_for_invalid_email()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Email no valido');
        
        new Email('invalid-email');
    }
    
    public function test_throws_exception_for_too_long_email()
    {
        $longEmail = str_repeat('a', 250) . '@example.com';
        
        $this->expectException(\InvalidArgumentException::class);
        
        new Email($longEmail);
    }
    
    public function test_throws_exception_for_blocked_domains()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Este email no es valido');
        
        new Email('test@tempmail.com');
    }
    
    public function test_emails_are_equal()
    {
        $email1 = new Email('test@example.com');
        $email2 = new Email('TEST@EXAMPLE.COM');
        
        $this->assertTrue($email1->equals($email2));
    }
    
    public function test_emails_are_not_equal()
    {
        $email1 = new Email('test1@example.com');
        $email2 = new Email('test2@example.com');
        
        $this->assertFalse($email1->equals($email2));
    }
    
    public function test_can_convert_to_string()
    {
        $email = new Email('test@example.com');
        
        $this->assertEquals('test@example.com', (string) $email);
    }
}