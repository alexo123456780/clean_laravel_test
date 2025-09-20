<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Usuario;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Password;
use App\Domain\Repositories\UsuarioRepositoryInterface;
use App\Infrastructure\Models\User;
use App\Infrastructure\Models\UserRole;
use Carbon\Carbon;

class EloquentUsuarioRepository implements UsuarioRepositoryInterface
{
    public function findById(int $id): ?Usuario
    {
        $user = User::with('userRoles')->find($id);
        
        if (!$user) {
            return null;
        }
        
        return $this->modelToEntity($user);
    }
    
    public function findByEmail(Email $email): ?Usuario
    {
        $user = User::with('userRoles')->where('email', $email->getValue())->first();
        
        if (!$user) {
            return null;
        }
        
        return $this->modelToEntity($user);
    }
    
    public function save(Usuario $usuario): Usuario
    {
        if ($usuario->getId()) {
            return $this->updateExisting($usuario);
        }
        
        return $this->createNew($usuario);
    }
    
    public function delete(int $id): bool
    {
        $user = User::find($id);
        
        if (!$user) {
            return false;
        }
        
        // Soft delete - just deactivate
        $user->update(['activo' => false]);
        
        return true;
    }
    
    public function findAll(int $page = 1, int $perPage = 15): array
    {
        $users = User::with('userRoles')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();
        
        return $users->map(fn($user) => $this->modelToEntity($user))->toArray();
    }
    
    public function findActive(): array
    {
        $users = User::with('userRoles')->where('activo', true)->get();
        
        return $users->map(fn($user) => $this->modelToEntity($user))->toArray();
    }
    
    public function findByRole(string $roleName): array
    {
        $users = User::with('userRoles')
            ->whereHas('userRoles', function ($query) use ($roleName) {
                $query->where('role_name', $roleName);
            })
            ->get();
        
        return $users->map(fn($user) => $this->modelToEntity($user))->toArray();
    }
    
    public function exists(Email $email): bool
    {
        return User::where('email', $email->getValue())->exists();
    }
    
    private function modelToEntity(User $user): Usuario
    {
        return new Usuario(
            nombre: $user->nombre,
            email: new Email($user->email),
            password: Password::fromHash($user->password),
            apellidoPaterno: $user->apellido_paterno,
            apellidoMaterno: $user->apellido_materno,
            activo: $user->activo,
            roles: $user->roles,
            id: $user->id,
            createdAt: $user->created_at,
            updatedAt: $user->updated_at
        );
    }
    
    private function createNew(Usuario $usuario): Usuario
    {
        $user = User::create([
            'nombre' => $usuario->getNombre(),
            'apellido_paterno' => $usuario->getApellidoPaterno(),
            'apellido_materno' => $usuario->getApellidoMaterno(),
            'email' => $usuario->getEmail()->getValue(),
            'password' => $usuario->getPassword()->getHash(),
            'activo' => $usuario->isActive(),
        ]);
        
        // Save roles
        foreach ($usuario->getRoles() as $role) {
            UserRole::create([
                'user_id' => $user->id,
                'role_name' => $role['name']
            ]);
        }
        
        return $this->findById($user->id);
    }
    
    private function updateExisting(Usuario $usuario): Usuario
    {
        $user = User::find($usuario->getId());
        
        $user->update([
            'nombre' => $usuario->getNombre(),
            'apellido_paterno' => $usuario->getApellidoPaterno(),
            'apellido_materno' => $usuario->getApellidoMaterno(),
            'email' => $usuario->getEmail()->getValue(),
            'password' => $usuario->getPassword()->getHash(),
            'activo' => $usuario->isActive(),
        ]);
        
        // Update roles - simple approach: delete all and recreate
        UserRole::where('user_id', $user->id)->delete();
        foreach ($usuario->getRoles() as $role) {
            UserRole::create([
                'user_id' => $user->id,
                'role_name' => $role['name']
            ]);
        }
        
        return $this->findById($user->id);
    }
}