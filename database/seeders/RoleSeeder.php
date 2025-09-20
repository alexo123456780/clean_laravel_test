<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Infrastructure\Models\User;
use App\Infrastructure\Models\UserRole;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios existentes para asignar roles adicionales
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios en la base de datos. Ejecuta UsuarioSeeder primero.');
            return;
        }

        // Roles disponibles en el sistema
        $availableRoles = [
            'admin',
            'moderator', 
            'editor',
            'user',
            'guest',
            'supervisor',
            'analyst'
        ];

        // Asignar roles aleatorios a algunos usuarios
        $users->random(min(5, $users->count()))->each(function ($user) use ($availableRoles) {
            // Obtener roles actuales del usuario
            $currentRoles = $user->userRoles->pluck('role_name')->toArray();
            
            // Seleccionar 1-3 roles aleatorios que no tenga ya
            $newRoles = collect($availableRoles)
                ->diff($currentRoles)
                ->random(min(3, count($availableRoles) - count($currentRoles)));
            
            foreach ($newRoles as $roleName) {
                UserRole::create([
                    'user_id' => $user->id,
                    'role_name' => $roleName
                ]);
            }
            
            $this->command->info("Roles asignados a {$user->nombre}: " . $newRoles->implode(', '));
        });

        $this->command->info('Roles adicionales asignados exitosamente.');
        $this->command->info('Roles disponibles en el sistema: ' . implode(', ', $availableRoles));
    }
}