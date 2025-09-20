<?php

namespace Tests\Unit\Domain\Entities;

use PHPUnit\Framework\TestCase;
use App\Domain\Entities\Usuario;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Password;
use Carbon\Carbon;

class UsuarioTest extends TestCase
{
    public function test_can_create_usuario_with_required_fields()
    {
        $email = new Email('test@example.com');
        $password = Password::fromPlainText('password123');
        
        $usuario = new Usuario(
            nombre: 'Juan',
            email: $email,
            password: $password
        );
        
        $this->assertEquals('Juan', $usuario->getNombre());
        $this->assertEquals($email, $usuario->getEmail());
        $this->assertEquals($password, $usuario->getPassword());
        $this->assertTrue($usuario->isActive());
        $this->assertEmpty($usuario->getRoles());
    }
    
    public function test_can_create_usuario_with_all_fields()
    {
        $email = new Email('test@example.com');
        $password = Password::fromPlainText('password123');
        $roles = [['name' => 'admin']];
        
        $usuario = new Usuario(
            nombre: 'Juan',
            email: $email,
            password: $password,
            apellidoPaterno: 'Pérez',
            apellidoMaterno: 'García',
            activo: false,
            roles: $roles,
            id: 1
        );
        
        $this->assertEquals(1, $usuario->getId());
        $this->assertEquals('Juan', $usuario->getNombre());
        $this->assertEquals('Pérez', $usuario->getApellidoPaterno());
        $this->assertEquals('García', $usuario->getApellidoMaterno());
        $this->assertEquals('Juan Pérez García', $usuario->getFullName());
        $this->assertFalse($usuario->isActive());
        $this->assertEquals($roles, $usuario->getRoles());
    }
    
    public function test_can_activate_and_deactivate_usuario()
    {
        $usuario = $this->createUsuario();
        
        $usuario->deactivate();
        $this->assertFalse($usuario->isActive());
        
        $usuario->activate();
        $this->assertTrue($usuario->isActive());
    }
    
    public function test_can_change_password()
    {
        $usuario = $this->createUsuario();
        $newPassword = Password::fromPlainText('newpassword123');
        
        $usuario->changePassword($newPassword);
        
        $this->assertEquals($newPassword, $usuario->getPassword());
    }
    
    public function test_can_update_profile()
    {
        $usuario = $this->createUsuario();
        
        $usuario->updateProfile('Carlos', 'López', 'Martínez');
        
        $this->assertEquals('Carlos', $usuario->getNombre());
        $this->assertEquals('López', $usuario->getApellidoPaterno());
        $this->assertEquals('Martínez', $usuario->getApellidoMaterno());
        $this->assertEquals('Carlos López Martínez', $usuario->getFullName());
    }
    
    public function test_can_manage_roles()
    {
        $usuario = $this->createUsuario();
        $adminRole = ['name' => 'admin'];
        $userRole = ['name' => 'user'];
        
        $usuario->assignRole($adminRole);
        $this->assertTrue($usuario->hasRole('admin'));
        
        $usuario->assignRole($userRole);
        $this->assertTrue($usuario->hasRole('user'));
        $this->assertCount(2, $usuario->getRoles());
        
        $usuario->removeRole('admin');
        $this->assertFalse($usuario->hasRole('admin'));
        $this->assertTrue($usuario->hasRole('user'));
        $this->assertCount(1, $usuario->getRoles());
    }
    
    public function test_throws_exception_for_empty_name()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('El nombre no puede estar vacío');
        
        new Usuario(
            nombre: '',
            email: new Email('test@example.com'),
            password: Password::fromPlainText('password123')
        );
    }
    
    private function createUsuario(): Usuario
    {
        return new Usuario(
            nombre: 'Juan',
            email: new Email('test@example.com'),
            password: Password::fromPlainText('password123')
        );
    }
}