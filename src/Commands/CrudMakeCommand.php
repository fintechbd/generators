<?php

namespace Fintech\Generator\Commands;

use Fintech\Generator\Traits\ModuleCommandTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class CrudMakeCommand
 */
class CrudMakeCommand extends Command
{
    use ModuleCommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'package:make-crud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new API Restful CRUD stub files';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->createRequests();

            $this->createResources();

            $this->createStubFiles();

            $this->createRepositories();

        } catch (\Throwable $exception) {
            $this->error($exception);
        }

    }

    protected function getResourceName()
    {
        return Str::studly($this->argument('name'));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the resource.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    //Create Request
    private function createRequests()
    {
        foreach (['Index', 'Store', 'Update', 'Import'] as $prefix) {

            $resourcePath = $this->getResourceName().'Request';

            $dir = dirname($resourcePath);

            $dir = ($dir == '.') ? '' : $dir.'/';

            $resource = basename($resourcePath);

            $options = [
                'name' => $dir.$prefix.$resource,
                'module' => $this->getModuleName(),
            ];

            if ($prefix == 'Index') {
                $options['--index'] = true;
            }

            if (in_array($prefix, ['Store', 'Update'])) {
                $options['--crud'] = true;
            }

            Artisan::call('package:make-request', $options);
        }
    }

    //Create Resource
    private function createResources()
    {
        Artisan::call('package:make-resource', [
            'name' => $this->getResourceName().'Resource',
            'module' => $this->getModuleName(),
        ]);

        Artisan::call('package:make-resource', [
            'name' => $this->getResourceName().'Collection',
            'module' => $this->getModuleName(),
            '--collection',
        ]);
    }

    //Create Controller,Model,Service and Interface etc.
    private function createStubFiles()
    {
        Artisan::call('package:make-controller', [
            'controller' => $this->getResourceName(),
            'module' => $this->getModuleName(),
            '--crud' => true,
        ]);

        Artisan::call('package:make-model', [
            'model' => $this->getResourceName(),
            'module' => $this->getModuleName(),
        ]);

        Artisan::call('package:make-service', [
            'name' => $this->getResourceName().'Service',
            'module' => $this->getModuleName(),
            '--crud' => true,
            '--repository' => $this->getResourceName().'Repository',
        ]);

    }

    private function createRepositories()
    {
        Artisan::call('package:make-exception', [
            'name' => $this->getResourceName().'RepositoryException',
            'module' => $this->getModuleName(),
        ]);

        Artisan::call('package:make-interface', [
            'name' => $this->getResourceName().'Repository',
            'module' => $this->getModuleName(),
            '--repository' => $this->getResourceName().'RepositoryException',
            '--crud' => true,
        ]);

        Artisan::call('package:make-repository', [
            'name' => 'Eloquent/'.$this->getResourceName().'Repository',
            'module' => $this->getModuleName(),
            '--repository' => $this->getResourceName().'RepositoryException',
            '--crud' => true,
        ]);

        Artisan::call('package:make-repository', [
            'name' => 'Mongodb/'.$this->getResourceName().'Repository',
            'module' => $this->getModuleName(),
            '--repository' => $this->getResourceName().'RepositoryException',
            '--crud' => true,
        ]);
    }
}
