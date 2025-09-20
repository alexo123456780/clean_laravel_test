<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Usuario;
use App\Domain\ValueObjects\Email;

interface UsuarioRepositoryInterface
{
    public function findById(int $id): ?Usuario;
    
    public function findByEmail(Email $email): ?Usuario;
    
    public function save(Usuario $usuario): Usuario;
    
    public function delete(int $id): bool;
    
    public function findAll(int $page = 1, int $perPage = 15): array;
    
    public function findActive(): array;
    
    public function findByRole(string $roleName): array;
    
    public function exists(Email $email): bool;
}