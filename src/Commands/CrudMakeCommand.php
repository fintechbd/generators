<?php

namespace Fintech\Generator\Commands;

use Fintech\Generator\Support\Config\GenerateConfigReader;
use Fintech\Generator\Support\Config\GeneratorPath;
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

            $this->createRoute();

            $this->createConfigOption();

            return self::SUCCESS;

        } catch (\Throwable $exception) {
            $this->error($exception);
        }
        return self::FAILURE;
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

            $resourcePath = $this->getResourceName() . 'Request';

            $dir = dirname($resourcePath);

            $dir = ($dir == '.') ? '' : $dir . '/';

            $resource = basename($resourcePath);

            $options = [
                'name' => $dir . $prefix . $resource,
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
            'name' => $this->getResourceName() . 'Resource',
            'module' => $this->getModuleName(),
        ]);

        Artisan::call('package:make-resource', [
            'name' => $this->getResourceName() . 'Collection',
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
            'name' => $this->getResourceName() . 'Service',
            'module' => $this->getModuleName(),
            '--crud' => true,
            '--repository' => $this->getResourceName() . 'Repository',
        ]);

    }

    private function createRepositories()
    {
        Artisan::call('package:make-interface', [
            'name' => $this->getResourceName() . 'Repository',
            'module' => $this->getModuleName(),
            '--crud' => true,
        ]);

        Artisan::call('package:make-repository', [
            'name' => 'Eloquent/' . $this->getResourceName() . 'Repository',
            'module' => $this->getModuleName(),
            '--model' => $this->getResourceName(),
            '--crud' => true,
        ]);

        Artisan::call('package:make-repository', [
            'name' => 'Mongodb/' . $this->getResourceName() . 'Repository',
            'module' => $this->getModuleName(),
            '--model' => $this->getResourceName(),
            '--crud' => true,
        ]);
    }

    private function createRoute()
    {
        $filePath = $this->getModulePath() . GenerateConfigReader::read('routes')->getPath() . DIRECTORY_SEPARATOR . 'api.php';

        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("Route file location doesn't exist");
        }

        $fileContent = file_get_contents($filePath);

        $singleName = Str::lower(basename($this->getResourceName()));

        $resourceName = Str::plural($singleName);

        $controller = GeneratorPath::convertPathToNamespace(
            $this->getModuleNS() .
            GenerateConfigReader::read('controller')->getNamespace()
            . '\\' . $this->getResourceName() . 'Controller::class'
        );

        $template = <<<HTML
Route::apiResource('{$resourceName}', {$controller});
    Route::post('{$resourceName}/{$singleName}/restore', [{$controller}, 'restore'])->name('{$resourceName}.restore');

    //DO NOT REMOVE THIS LINE//
HTML;

        $fileContent = str_replace('//DO NOT REMOVE THIS LINE//', $template, $fileContent);

        file_put_contents($filePath, $fileContent);
    }

    private function createConfigOption()
    {

        $filePath = $this->getModulePath() . GenerateConfigReader::read('config')->getPath()
            . DIRECTORY_SEPARATOR . strtolower($this->getModuleName()) . '.php';

        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("`{$filePath}` is invalid config file path");
        }

        $fileContent = file_get_contents($filePath);

        $singleName = basename($this->getResourceName());

        $lowerName = Str::lower($singleName);

        $model = GeneratorPath::convertPathToNamespace(
            $this->getModuleNS() .
            GenerateConfigReader::read('model')->getNamespace() .
            '\\' . $singleName . '::class'
        );

        $interfacePath = GeneratorPath::convertPathToNamespace(
            $this->getModuleNS() .
            GenerateConfigReader::read('interface')->getNamespace() .
            '\\' . $singleName . 'Repository::class'
        );

        $repositoryPath = GeneratorPath::convertPathToNamespace(
            $this->getModuleNS() .
            GenerateConfigReader::read('repository')->getNamespace() .
            '\\Eloquent\\' . $this->getResourceName() . 'Repository::class'
        );

        $modelOptionTemplate = <<<HTML

    /*
    |--------------------------------------------------------------------------
    | {$singleName} Model
    |--------------------------------------------------------------------------
    |
    | This value will be used to across system where model is needed
    */
    '{$lowerName}_model' => {$model},

    //** Model Config Point Do not Remove **//
HTML;
        $repoOptionTemplate = <<<HTML
        {$interfacePath} => {$repositoryPath},

        //** Repository Binding Config Point Do not Remove **//
HTML;


        $replacements = [
            '//** Model Config Point Do not Remove **//' => $modelOptionTemplate,
            '//** Repository Binding Config Point Do not Remove **//' => $repoOptionTemplate
        ];

        $fileContent = str_replace(array_keys($replacements), array_values($replacements), $fileContent);

        file_put_contents($filePath, $fileContent);
    }

}
