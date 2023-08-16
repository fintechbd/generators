<?php

namespace Fintech\Generator\Providers;

use Illuminate\Support\ServiceProvider;
use Fintech\Generator\Contracts\RepositoryInterface;
use Fintech\Generator\Laravel\LaravelFileRepository;

class ContractsServiceProvider extends ServiceProvider
{
    /**
     * Register some binding.
     */
    public function register()
    {
        $this->app->bind(RepositoryInterface::class, LaravelFileRepository::class);
    }
}
