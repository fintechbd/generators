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
        //        $this->createRequests();
        //
        //        $this->createResources();

        $this->createStubFiles();

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
            $options = [
                'name' => $prefix.$this->getResourceName().'Request',
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
        ]);
        Artisan::call('package:make-resource', [
            'name' => $this->getResourceName().'Collection',
            '--collection',
        ]);
    }

    //Create Controller,Model,Service and Interface etc.
    private function createStubFiles()
    {
        Artisan::call('package:make-model', [
            'name' => $this->getResourceName(),
        ]);

        Artisan::call('package:make-controller', [
            'name' => $this->getResourceName(),
            '--crud',
        ]);

        Artisan::call('package:make-service', [
            'name' => $this->getResourceName().'Service',
        ]);

        Artisan::call('package:make-interface', [
            'name' => $this->getResourceName().'Repository',
            '--crud',
        ]);
    }

    private function createRepositories()
    {
        Artisan::call('package:make-repository', [
            'name' => 'Eloquent/'.$this->getResourceName().'Repository',
        ]);

        Artisan::call('package:make-repository', [
            'name' => 'Mongodb/'.$this->getResourceName().'Repository',
        ]);
    }
}
