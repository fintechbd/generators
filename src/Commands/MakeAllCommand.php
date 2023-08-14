<?php

namespace Fintech\Generator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class MakeAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $resource = $this->argument('resource');

//        $version = $this->option('version');

        if (empty($resource)) {
            throw new \InvalidArgumentException('API Resource class name is missing.');
        }

        $resource = ucwords($resource);

        $indexRequest = 'Index' . $resource . 'Request';

        $importRequest = 'Import' . $resource . 'Request';

        Artisan::call('make:request', ['name' => $indexRequest]);

        Artisan::call('make:request', ['name' => $importRequest]);

        Artisan::call('make:exception', ['name' => $resource . 'RepositoryException']);

        Artisan::call('make:model', [
            'name' => $resource,
            '--seed' => true,
            '--migration' => true,
            '--controller' => true,
            '--api' => true,
            '--requests' => true,
            '--test' => true
            ]);

        Artisan::call('make:resource', ['name' => $resource.'Resource']);

        Artisan::call('make:resource', ['name' => $resource.'Collection', '--collection']);

//        Artisan::call('make:service', ['name' => $resource.'Service', 'resource' => $resource]);

        $this->addMethodToMaster($resource);

        $this->addBindToProvider($resource);
    }

    private function addMethodToMaster(string $resource)
    {
        $serviceClass = $resource.'Service';

        $ns = "\App\Services\\$serviceClass";

        $method = Str::camel($resource);

        $template = <<<HTML
/**
     *
     * Return a new instance of $resource service class instance
     * to handle create, read, update, delete, restore , export & import
     * operation
     *
     * @return $ns
     * @throws BindingResolutionException
     */
    public function $method(): $ns
    {
        return app()->make($ns::class);
    }

    //DO NOT REMOVE THE COMMENT//
HTML;
        $masterPath = app_path('MetaDataService.php');
        $fileContent = file_get_contents($masterPath);

        file_put_contents($masterPath, str_replace('//DO NOT REMOVE THE COMMENT//', $template, $fileContent));

    }

    private function addBindToProvider(string $resource)
    {
        $repoClass = $resource.'Repository';

        $template = <<<HTML
\App\Interfaces\\$repoClass::class => [
            'mongodb' => \App\Repositories\Mongodb\\$repoClass::class,
            'default' => \App\Repositories\Eloquent\\$repoClass::class,
        ],
        //DO NOT REMOVE THE COMMENT//
HTML;
        $masterPath = app_path('Providers/RepositoryServiceProvider.php');
        $fileContent = file_get_contents($masterPath);

        file_put_contents($masterPath, str_replace('//DO NOT REMOVE THE COMMENT//', $template, $fileContent));

    }
}
