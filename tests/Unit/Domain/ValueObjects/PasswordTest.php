<?php

namespace Tests\Unit\Domain\ValueObjects;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObjects\Password;

class PasswordTest extends TestCase
{
    public function test_can_create_password_from_plain_text()
    {
        $password = Password::fromPlainText('password123');
        
        $this->assertEquals('password123', $password->getPlainText());
        $this->assertNotEmpty($password->getHash());
        $this->assertTrue($password->verify('password123'));
    }
    
    public function test_can_create_password_from_hash()
    {
        $hash = password_hash('password123', PASSWORD_ARGON2I);
        $password = Password::fromHash($hash);
        
        $this->assertEquals($hash, $password->getHash());
        $this->assertNull($password->getPlainText());
        $this->assertTrue($password->verify('password123'));
    }
    
    public function test_passwords_with_same_plain_text_are_not_equal()
    {
        $password1 = Password::fromPlainText('password123');
        $password2 = Password::fromPlainText('password123');
        
        // Los hashes son diferentes debido al salt
        $this->assertFalse($password1->equals($password2));
    }
    
    public function test_passwords_with_same_hash_are_equal()
    {
        $hash = password_hash('password123', PASSWORD_ARGON2I);
        $password1 = Password::fromHash($hash);
        $password2 = Password::fromHash($hash);
        
        $this->assertTrue($password1->equals($password2));
    }
    
    public function test_throws_exception_for_empty_password()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('El password no debe estar vacio');
        
        Password::fromPlainText('');
    }
    
    public function test_throws_exception_for_short_password()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('El password no debe ser menor a 8 caracteres');
        
        Password::fromPlainText('1234567');
    }
    
    public function test_throws_exception_for_long_password()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('El password no debe ser mayor a 20 caracteres');
        
        Password::fromPlainText('123456789012345678901');
    }
    
    public function test_throws_exception_for_empty_hash()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('El hash no puede estar vacío');
        
        Password::fromHash('');
    }
    
    public function test_verify_returns_false_for_wrong_password()
    {
        $password = Password::fromPlainText('password123');
        
        $this->assertFalse($password->verify('wrongpassword'));
    }
}