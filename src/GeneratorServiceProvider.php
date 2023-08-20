<?php

namespace Fintech\Generator;

use Illuminate\Support\ServiceProvider;
use Fintech\Generator\Commands\InstallCommand;
use Fintech\Generator\Commands\GeneratorCommand;

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
            __DIR__.'/../config/generators.php', 'generators'
        );
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/generators.php' => config_path('generators.php'),
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
                InstallCommand::class,
                GeneratorCommand::class,
            ]);
        }
    }
}
