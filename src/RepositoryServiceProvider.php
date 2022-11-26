<?php
namespace Tungnt\LaravelRepository;

use Illuminate\Support\ServiceProvider;

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
            CreateRepositoryCommand::class,
            CreateTraitCommand::class,
            CreateServiceCommand::class,
            CreateBladeCommand::class,
            ClearLogCommand::class,

            // For nWidart/laravel-modules:
            CreateModuleRepositoryCommand::class,
            CreateModuleTraitCommand::class,
            CreateModuleServiceCommand::class,
            CreateModuleBladeCommand::class
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}