<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Infrastructure\Models\User;
use App\Infrastructure\Models\UserRole;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuario administrador
        $admin = User::create([
            'nombre' => 'Juan Carlos',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'García',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'activo' => true,
        ]);

        // Asignar rol de administrador
        UserRole::create([
            'user_id' => $admin->id,
            'role_name' => 'admin'
        ]);

        UserRole::create([
            'user_id' => $admin->id,
            'role_name' => 'user'
        ]);

        // Usuario regular
        $user = User::create([
            'nombre' => 'María Elena',
            'apellido_paterno' => 'López',
            'apellido_materno' => 'Martínez',
            'email' => 'maria@example.com',
            'password' => Hash::make('user123'),
            'activo' => true,
        ]);

        // Asignar rol de usuario
        UserRole::create([
            'user_id' => $user->id,
            'role_name' => 'user'
        ]);

        // Usuario con solo nombre
        $simpleUser = User::create([
            'nombre' => 'Carlos',
            'apellido_paterno' => null,
            'apellido_materno' => null,
            'email' => 'carlos@example.com',
            'password' => Hash::make('password123'),
            'activo' => true,
        ]);

        UserRole::create([
            'user_id' => $simpleUser->id,
            'role_name' => 'user'
        ]);

        // Usuario inactivo
        $inactiveUser = User::create([
            'nombre' => 'Ana',
            'apellido_paterno' => 'Rodríguez',
            'apellido_materno' => 'Fernández',
            'email' => 'ana@example.com',
            'password' => Hash::make('password123'),
            'activo' => false,
        ]);

        UserRole::create([
            'user_id' => $inactiveUser->id,
            'role_name' => 'user'
        ]);

        // Usuario moderador
        $moderator = User::create([
            'nombre' => 'Luis',
            'apellido_paterno' => 'González',
            'apellido_materno' => 'Ruiz',
            'email' => 'luis@example.com',
            'password' => Hash::make('moderator123'),
            'activo' => true,
        ]);

        UserRole::create([
            'user_id' => $moderator->id,
            'role_name' => 'moderator'
        ]);

        UserRole::create([
            'user_id' => $moderator->id,
            'role_name' => 'user'
        ]);

        // Crear usuarios adicionales usando el factory
        User::factory(10)->create()->each(function ($user) {
            UserRole::create([
                'user_id' => $user->id,
                'role_name' => 'user'
            ]);
        });

        // Crear algunos usuarios inactivos usando el factory
        User::factory(3)->inactive()->create()->each(function ($user) {
            UserRole::create([
                'user_id' => $user->id,
                'role_name' => 'user'
            ]);
        });

        $this->command->info('Usuarios creados exitosamente:');
        $this->command->info('- Admin: admin@example.com / admin123');
        $this->command->info('- Usuario: maria@example.com / user123');
        $this->command->info('- Simple: carlos@example.com / password123');
        $this->command->info('- Inactivo: ana@example.com / password123');
        $this->command->info('- Moderador: luis@example.com / moderator123');
        $this->command->info('- 10 usuarios adicionales generados con factory');
        $this->command->info('- 3 usuarios inactivos generados con factory');
    }
}