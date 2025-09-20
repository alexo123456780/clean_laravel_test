<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Application\UseCases\ListUsuariosUseCase;

class ListUsuariosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usuario:list 
                            {--page=1 : Página a mostrar}
                            {--per-page=10 : Usuarios por página}
                            {--active-only : Solo mostrar usuarios activos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listar usuarios del sistema';

    public function __construct(
        private ListUsuariosUseCase $listUsuariosUseCase
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            if ($this->option('active-only')) {
                $usuarios = $this->listUsuariosUseCase->executeActiveOnly();
                $this->info('Mostrando solo usuarios activos:');
                $data = $usuarios;
            } else {
                $page = (int) $this->option('page');
                $perPage = (int) $this->option('per-page');
                
                $result = $this->listUsuariosUseCase->execute($page, $perPage);
                $data = $result['data'];
                
                $this->info("Página {$page} de usuarios (mostrando {$perPage} por página):");
            }

            if (empty($data)) {
                $this->warn('No se encontraron usuarios.');
                return Command::SUCCESS;
            }

            $tableData = [];
            foreach ($data as $usuario) {
                $roles = implode(', ', array_column($usuario->roles, 'name'));
                $tableData[] = [
                    $usuario->id,
                    $usuario->fullName,
                    $usuario->email,
                    $usuario->activo ? '✓' : '✗',
                    $roles ?: 'Sin roles',
                    $usuario->createdAt
                ];
            }

            $this->table(
                ['ID', 'Nombre Completo', 'Email', 'Activo', 'Roles', 'Creado'],
                $tableData
            );

            if (!$this->option('active-only')) {
                $this->info("Total en esta página: " . count($data));
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}