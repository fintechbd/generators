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

class RepositoryMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The stub file type
     *
     * @var string
     */
    protected $type = 'repository';

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'package:make-repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new repository for the specified package.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the command.'],
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
            ['crud', null, InputOption::VALUE_NONE, 'The terminal command that should be assigned.'],
            ['repository', null, InputOption::VALUE_REQUIRED, 'The terminal command that should be assigned.'],
        ];
    }

    /**
     * @return mixed
     *
     * @throws GeneratorException
     */
    protected function getTemplateContents()
    {
        $replacements = [
            'CLASS_NAMESPACE' => $this->getClassNamespace($this->getModuleName()),
            'CLASS' => $this->getClass(),
            'LOWER_MODULE' => Str::lower($this->getModuleName()),
            'MODULE' => $this->getModuleName(),
            'NAMESPACE' => config('fintech.generators.namespace'),
            'EXCEPTION_NAMESPACE' => $this->setExceptionNS(),
            'EXCEPTION' => $this->getClass() . 'Exception',
        ];

        return (new Stub($this->getStub(), $replacements))->render();
    }

    private function setExceptionNS()
    {

        $ns = 'use ' . config('fintech.generators.namespace')
            . '/' . $this->getModuleName()
            . '/' . 'Exceptions'
            . '/' . $this->argument($this->argumentName)
            . ';';

        return implode('\\', explode('/', $ns));

    }

    protected function getStub()
    {
        return ($this->option('crud'))
            ? '/repository-crud.stub'
            : '/repository.stub';
    }

    /**
     * @return mixed
     *
     * @throws GeneratorException
     */
    protected function getDestinationFilePath()
    {
        $path = $this->getModulePath($this->getModuleName());

        $commandPath = GenerateConfigReader::read('repository');

        return $path.$commandPath->getPath().'/'.$this->getFileName().'.php';
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name'));
    }
}
