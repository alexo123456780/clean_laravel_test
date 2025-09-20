<?php

namespace App\Domain\Entities;

use Carbon\Carbon;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Password;


class Usuario{

    private ?int $id;
    private string $nombre;
    private ?string $apellidoPaterno;
    private ?string $apellidoMaterno;
    private Email $email;
    private Password $password;
    private array $roles;
    private Carbon $createdAt;
    private Carbon $updatedAt;
    private bool $activo;

    public function __construct(
        string $nombre,
        Email $email,
        Password $password,
        ?string $apellidoPaterno = null,
        ?string $apellidoMaterno = null,
        bool $activo = true,
        array $roles = [],
        ?int $id = null,
        ?Carbon $createdAt = null,
        ?Carbon $updatedAt = null
    )
    {
        $this->validateNombre($nombre);
        
        $this->id = $id;
        $this->nombre = $nombre;
        $this->apellidoPaterno = $apellidoPaterno;
        $this->apellidoMaterno = $apellidoMaterno;
        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
        $this->createdAt = $createdAt ?? Carbon::now();
        $this->updatedAt = $updatedAt ?? Carbon::now();
        $this->activo = $activo;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getApellidoPaterno(): ?string
    {
        return $this->apellidoPaterno;
    }

    public function getApellidoMaterno(): ?string
    {
        return $this->apellidoMaterno;
    }

    public function getFullName(): string
    {
        $fullName = $this->nombre;
        
        if ($this->apellidoPaterno) {
            $fullName .= ' ' . $this->apellidoPaterno;
        }
        
        if ($this->apellidoMaterno) {
            $fullName .= ' ' . $this->apellidoMaterno;
        }
        
        return $fullName;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->updatedAt;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function isActive(): bool
    {
        return $this->activo;
    }


    // Domain methods
    public function hasRole(string $roleName): bool
    {
        return in_array($roleName, array_column($this->roles, 'name'));
    }

    public function assignRole(array $role): void
    {
        if (!$this->hasRole($role['name'])) {
            $this->roles[] = $role;
            $this->updatedAt = Carbon::now();
        }
    }

    public function removeRole(string $roleName): void
    {
        $this->roles = array_filter($this->roles, function($role) use ($roleName) {
            return $role['name'] !== $roleName;
        });
        $this->updatedAt = Carbon::now();
    }

    public function activate(): void
    {
        if (!$this->activo) {
            $this->activo = true;
            $this->updatedAt = Carbon::now();
        }
    }

    public function deactivate(): void
    {
        if ($this->activo) {
            $this->activo = false;
            $this->updatedAt = Carbon::now();
        }
    }

    public function changePassword(Password $newPassword): void
    {
        $this->password = $newPassword;
        $this->updatedAt = Carbon::now();
    }

    public function updateProfile(string $nombre, ?string $apellidoPaterno = null, ?string $apellidoMaterno = null): void
    {
        $this->validateNombre($nombre);
        
        $this->nombre = $nombre;
        $this->apellidoPaterno = $apellidoPaterno;
        $this->apellidoMaterno = $apellidoMaterno;
        $this->updatedAt = Carbon::now();
    }

    private function validateNombre(string $nombre): void{
        if (empty(trim($nombre))) {
            throw new \InvalidArgumentException('El nombre no puede estar vacío');
        }

        if (strlen($nombre) > 255) {
            throw new \InvalidArgumentException('El nombre no puede exceder 255 caracteres');
        }
    }












}
