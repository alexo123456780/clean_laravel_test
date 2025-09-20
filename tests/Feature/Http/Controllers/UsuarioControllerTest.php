<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Infrastructure\Models\User;

class UsuarioControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_usuario()
    {
        $userData = [
            'nombre' => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'García',
            'email' => 'juan@example.com',
            'password' => 'password123',
            'roles' => [
                ['name' => 'user']
            ]
        ];

        $response = $this->postJson('/api/usuarios', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'nombre',
                        'apellido_paterno',
                        'apellido_materno',
                        'full_name',
                        'email',
                        'roles',
                        'activo',
                        'created_at'
                    ],
                    'message'
                ]);

        $this->assertDatabaseHas('users', [
            'nombre' => 'Juan',
            'email' => 'juan@example.com',
            'activo' => true
        ]);
    }

    public function test_can_get_usuario()
    {
        $user = User::create([
            'nombre' => 'Juan',
            'apellido_paterno' => 'Pérez',
            'email' => 'juan@example.com',
            'password' => password_hash('password123', PASSWORD_ARGON2I),
            'activo' => true
        ]);

        $response = $this->getJson("/api/usuarios/{$user->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'nombre',
                        'apellido_paterno',
                        'full_name',
                        'email',
                        'activo'
                    ]
                ]);
    }

    public function test_can_update_usuario()
    {
        $user = User::create([
            'nombre' => 'Juan',
            'email' => 'juan@example.com',
            'password' => password_hash('password123', PASSWORD_ARGON2I),
            'activo' => true
        ]);

        $updateData = [
            'nombre' => 'Carlos',
            'apellido_paterno' => 'López'
        ];

        $response = $this->putJson("/api/usuarios/{$user->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonPath('data.nombre', 'Carlos')
                ->assertJsonPath('data.apellido_paterno', 'López');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'nombre' => 'Carlos',
            'apellido_paterno' => 'López'
        ]);
    }

    public function test_can_delete_usuario()
    {
        $user = User::create([
            'nombre' => 'Juan',
            'email' => 'juan@example.com',
            'password' => password_hash('password123', PASSWORD_ARGON2I),
            'activo' => true
        ]);

        $response = $this->deleteJson("/api/usuarios/{$user->id}");

        $response->assertStatus(200)
                ->assertJsonPath('message', 'Usuario desactivado exitosamente');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'activo' => false
        ]);
    }

    public function test_can_list_usuarios()
    {
        User::create([
            'nombre' => 'Juan',
            'email' => 'juan@example.com',
            'password' => password_hash('password123', PASSWORD_ARGON2I),
            'activo' => true
        ]);

        User::create([
            'nombre' => 'María',
            'email' => 'maria@example.com',
            'password' => password_hash('password123', PASSWORD_ARGON2I),
            'activo' => true
        ]);

        $response = $this->getJson('/api/usuarios');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'nombre',
                            'email',
                            'activo'
                        ]
                    ],
                    'pagination'
                ]);
    }

    public function test_validation_errors_on_create()
    {
        $response = $this->postJson('/api/usuarios', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['nombre', 'email', 'password']);
    }

    public function test_returns_404_for_non_existent_usuario()
    {
        $response = $this->getJson('/api/usuarios/999');

        $response->assertStatus(404)
                ->assertJsonPath('error.type', 'usuario_not_found');
    }

    public function test_returns_409_for_duplicate_email()
    {
        User::create([
            'nombre' => 'Juan',
            'email' => 'juan@example.com',
            'password' => password_hash('password123', PASSWORD_ARGON2I),
            'activo' => true
        ]);

        $userData = [
            'nombre' => 'María',
            'email' => 'juan@example.com', // Email duplicado
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/usuarios', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }
}