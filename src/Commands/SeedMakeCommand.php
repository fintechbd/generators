<?php

namespace Fintech\Generator\Commands;

use Fintech\Generator\Abstracts\GeneratorCommand;
use Fintech\Generator\Exceptions\GeneratorException;
use Fintech\Generator\Support\Config\GenerateConfigReader;
use Fintech\Generator\Support\Stub;
use Fintech\Generator\Traits\ModuleCommandTrait;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SeedMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The stub file type
     *
     * @var string
     */
    protected $type = 'seeder';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'package:make-seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new seeder for the specified package.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of seeder will be created.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'master',
                null,
                InputOption::VALUE_NONE,
                'Indicates the seeder will created is a master database seeder.',
            ],
        ];
    }

    /**
     * @return mixed
     * @throws GeneratorException
     */
    protected function getTemplateContents()
    {
        return (new Stub('/seeder.stub', [
            'NAME' => $this->getSeederName() . 'Seeder',
            'SERVICE_NAME' => Str::camel($this->getSeederName()),
            'MODULE' => $this->getModuleName(),
            'ROOT_NAMESPACE' => config('fintech.generators.namespace'),
            'NAMESPACE' => $this->getClassNamespace($this->getModuleName()),
        ]))->render();
    }

    /**
     * Get seeder name.
     *
     * @return string
     */
    private function getSeederName()
    {
        return Str::studly($this->argument('name'));
    }

    /**
     * @return mixed
     *
     * @throws GeneratorException
     */
    protected function getDestinationFilePath()
    {

        $path = $this->getModulePath($this->getModuleName());

        $seederPath = GenerateConfigReader::read($this->type);

        return $path . $seederPath->getPath() . '/' . $this->getSeederName() . 'Seeder.php';
    }
}
