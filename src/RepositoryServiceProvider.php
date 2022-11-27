<?php

namespace Tungnt\LaravelRepository;

use Illuminate\Support\ServiceProvider;
use Tungnt\LaravelRepository\Commands\MakeRepositoryCommand;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            MakeRepositoryCommand::class,
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/repository.php' => config_path('repository.php'),
        ], 'tungnt/config');
        $this->publishes([
            __DIR__ . '/Repositories/Interfaces/RepositoryInterface.php' => app_path('Repositories/Interfaces/RepositoryInterface.php'),
            __DIR__ . '/Repositories/BaseRepository.php' => app_path('Repositories/BaseRepository.php'),
        ], 'tungnt/Repositories');
    }
}
