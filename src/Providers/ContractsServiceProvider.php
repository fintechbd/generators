<?php

namespace Fintech\Generator\Providers;

use Fintech\Generator\Contracts\RepositoryInterface;
use Fintech\Generator\Laravel\LaravelFileRepository;
use Illuminate\Support\ServiceProvider;

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
