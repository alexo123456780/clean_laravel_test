<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Factories\UserFactory;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'email',
        'password',
        'activo',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }
    
    public function getRolesAttribute(): array
    {
        return $this->userRoles->map(function ($userRole) {
            return [
                'name' => $userRole->role_name,
                'assigned_at' => $userRole->created_at
            ];
        })->toArray();
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }
}