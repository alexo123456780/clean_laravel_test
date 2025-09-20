<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Repositories\UsuarioRepositoryInterface;
use App\Domain\Services\UsuarioServiceInterface;
use App\Domain\Services\UsuarioService;
use App\Infrastructure\Repositories\EloquentUsuarioRepository;
use App\Application\UseCases\CreateUsuarioUseCase;
use App\Application\UseCases\GetUsuarioUseCase;
use App\Application\UseCases\UpdateUsuarioUseCase;
use App\Application\UseCases\DeleteUsuarioUseCase;
use App\Application\UseCases\ListUsuariosUseCase;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(
            UsuarioRepositoryInterface::class,
            EloquentUsuarioRepository::class
        );
        
        // Service bindings
        $this->app->bind(
            UsuarioServiceInterface::class,
            UsuarioService::class
        );
        
        // Use case bindings
        $this->app->bind(CreateUsuarioUseCase::class, function ($app) {
            return new CreateUsuarioUseCase(
                $app->make(UsuarioServiceInterface::class)
            );
        });
        
        $this->app->bind(GetUsuarioUseCase::class, function ($app) {
            return new GetUsuarioUseCase(
                $app->make(UsuarioRepositoryInterface::class)
            );
        });
        
        $this->app->bind(UpdateUsuarioUseCase::class, function ($app) {
            return new UpdateUsuarioUseCase(
                $app->make(UsuarioServiceInterface::class)
            );
        });
        
        $this->app->bind(DeleteUsuarioUseCase::class, function ($app) {
            return new DeleteUsuarioUseCase(
                $app->make(UsuarioServiceInterface::class)
            );
        });
        
        $this->app->bind(ListUsuariosUseCase::class, function ($app) {
            return new ListUsuariosUseCase(
                $app->make(UsuarioRepositoryInterface::class)
            );
        });
    }

    public function boot(): void
    {
        //
    }
}