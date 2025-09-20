<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Application\UseCases\CreateUsuarioUseCase;
use App\Application\DTOs\CreateUsuarioRequest;
use App\Domain\Exceptions\DomainException;

class CreateUsuarioCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usuario:create 
                            {nombre : Nombre del usuario}
                            {email : Email del usuario}
                            {password : Contraseña del usuario}
                            {--apellido-paterno= : Apellido paterno}
                            {--apellido-materno= : Apellido materno}
                            {--roles=* : Roles del usuario (ej: admin,user)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear un nuevo usuario usando Clean Architecture';

    public function __construct(
        private CreateUsuarioUseCase $createUsuarioUseCase
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $roles = [];
            foreach ($this->option('roles') as $roleName) {
                $roles[] = ['name' => $roleName];
            }

            $request = new CreateUsuarioRequest(
                nombre: $this->argument('nombre'),
                email: $this->argument('email'),
                password: $this->argument('password'),
                apellidoPaterno: $this->option('apellido-paterno'),
                apellidoMaterno: $this->option('apellido-materno'),
                roles: $roles
            );

            $response = $this->createUsuarioUseCase->execute($request);

            $this->info('Usuario creado exitosamente:');
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['ID', $response->id],
                    ['Nombre Completo', $response->fullName],
                    ['Email', $response->email],
                    ['Activo', $response->activo ? 'Sí' : 'No'],
                    ['Roles', implode(', ', array_column($response->roles, 'name'))],
                    ['Creado', $response->createdAt],
                ]
            );

            return Command::SUCCESS;

        } catch (DomainException $e) {
            $this->error('Error del dominio: ' . $e->getMessage());
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}