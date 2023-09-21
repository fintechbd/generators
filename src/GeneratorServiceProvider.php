<?php

namespace Fintech\Generator;

use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/generators.php', 'fintech.generators'
        );
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/generators.php' => config_path('fintech/generators.php'),
        ]);

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'generators');

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/generators'),
        ]);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'generators');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/generators'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Fintech\Generator\Commands\CommandMakeCommand::class,
                \Fintech\Generator\Commands\ComponentClassMakeCommand::class,
                \Fintech\Generator\Commands\ComponentViewMakeCommand::class,
                \Fintech\Generator\Commands\CrudMakeCommand::class,
                \Fintech\Generator\Commands\ControllerMakeCommand::class,
                \Fintech\Generator\Commands\EventMakeCommand::class,
                \Fintech\Generator\Commands\ExceptionMakeCommand::class,
                \Fintech\Generator\Commands\InterfaceMakeCommand::class,
                \Fintech\Generator\Commands\JobMakeCommand::class,
                \Fintech\Generator\Commands\ListenerMakeCommand::class,
                \Fintech\Generator\Commands\MailMakeCommand::class,
                \Fintech\Generator\Commands\MiddlewareMakeCommand::class,
                \Fintech\Generator\Commands\NotificationMakeCommand::class,
                \Fintech\Generator\Commands\ProviderMakeCommand::class,
                \Fintech\Generator\Commands\RouteProviderMakeCommand::class,
                \Fintech\Generator\Commands\InstallCommand::class,
                \Fintech\Generator\Commands\ModuleMakeCommand::class,
                \Fintech\Generator\Commands\FactoryMakeCommand::class,
                \Fintech\Generator\Commands\PolicyMakeCommand::class,
                \Fintech\Generator\Commands\RequestMakeCommand::class,
                \Fintech\Generator\Commands\RepositoryMakeCommand::class,
                \Fintech\Generator\Commands\RuleMakeCommand::class,
                \Fintech\Generator\Commands\MigrationMakeCommand::class,
                \Fintech\Generator\Commands\ModelMakeCommand::class,
                \Fintech\Generator\Commands\SeedMakeCommand::class,
                \Fintech\Generator\Commands\ServiceMakeCommand::class,
                \Fintech\Generator\Commands\UnUseCommand::class,
                \Fintech\Generator\Commands\UseCommand::class,
                \Fintech\Generator\Commands\ResourceMakeCommand::class,
                \Fintech\Generator\Commands\TestMakeCommand::class,
            ]);
        }
    }
}
