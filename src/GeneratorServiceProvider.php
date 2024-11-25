<?php

namespace Fintech\Generator;

use Fintech\Core\Traits\Packages\RegisterPackageTrait;
use Fintech\Generator\Commands\CommandMakeCommand;
use Fintech\Generator\Commands\ComponentClassMakeCommand;
use Fintech\Generator\Commands\ComponentViewMakeCommand;
use Fintech\Generator\Commands\ControllerMakeCommand;
use Fintech\Generator\Commands\CrudMakeCommand;
use Fintech\Generator\Commands\EventMakeCommand;
use Fintech\Generator\Commands\ExceptionMakeCommand;
use Fintech\Generator\Commands\FactoryMakeCommand;
use Fintech\Generator\Commands\InstallCommand;
use Fintech\Generator\Commands\InterfaceMakeCommand;
use Fintech\Generator\Commands\JobMakeCommand;
use Fintech\Generator\Commands\ListenerMakeCommand;
use Fintech\Generator\Commands\MailMakeCommand;
use Fintech\Generator\Commands\MiddlewareMakeCommand;
use Fintech\Generator\Commands\MigrationMakeCommand;
use Fintech\Generator\Commands\ModelMakeCommand;
use Fintech\Generator\Commands\ModuleMakeCommand;
use Fintech\Generator\Commands\NotificationMakeCommand;
use Fintech\Generator\Commands\PolicyMakeCommand;
use Fintech\Generator\Commands\ProviderMakeCommand;
use Fintech\Generator\Commands\RepositoryMakeCommand;
use Fintech\Generator\Commands\RequestMakeCommand;
use Fintech\Generator\Commands\ResourceMakeCommand;
use Fintech\Generator\Commands\RouteProviderMakeCommand;
use Fintech\Generator\Commands\RuleMakeCommand;
use Fintech\Generator\Commands\SeedMakeCommand;
use Fintech\Generator\Commands\ServiceMakeCommand;
use Fintech\Generator\Commands\TestMakeCommand;
use Fintech\Generator\Commands\UnUseCommand;
use Fintech\Generator\Commands\UseCommand;
use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
{
    use RegisterPackageTrait;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->packageCode = 'generators';

        $this->mergeConfigFrom(
            __DIR__ . '/../config/generators.php',
            'fintech.generators'
        );
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->injectOnConfig();

        $this->publishes([
            __DIR__ . '/../config/generators.php' => config_path('fintech/generators.php'),
        ]);

        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'generators');

        $this->publishes([
            __DIR__ . '/../lang' => $this->app->langPath('vendor/generators'),
        ]);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'generators');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/generators'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                CommandMakeCommand::class,
                ComponentClassMakeCommand::class,
                ComponentViewMakeCommand::class,
                CrudMakeCommand::class,
                ControllerMakeCommand::class,
                EventMakeCommand::class,
                ExceptionMakeCommand::class,
                InterfaceMakeCommand::class,
                JobMakeCommand::class,
                ListenerMakeCommand::class,
                MailMakeCommand::class,
                MiddlewareMakeCommand::class,
                NotificationMakeCommand::class,
                ProviderMakeCommand::class,
                RouteProviderMakeCommand::class,
                InstallCommand::class,
                ModuleMakeCommand::class,
                FactoryMakeCommand::class,
                PolicyMakeCommand::class,
                RequestMakeCommand::class,
                RepositoryMakeCommand::class,
                RuleMakeCommand::class,
                MigrationMakeCommand::class,
                ModelMakeCommand::class,
                SeedMakeCommand::class,
                ServiceMakeCommand::class,
                UnUseCommand::class,
                UseCommand::class,
                ResourceMakeCommand::class,
                TestMakeCommand::class,
            ]);
        }
    }
}
