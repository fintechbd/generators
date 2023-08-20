<?php

namespace Fintech\Generator\Commands;

use Fintech\Generator\Abstracts\GeneratorCommand;
use Fintech\Generator\Support\Config\GenerateConfigReader;
use Fintech\Generator\Support\Stub;
use Fintech\Generator\Traits\ModuleCommandTrait;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class RuleMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The stub file type
     *
     * @var string
     */
    protected $type = 'rule';

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
    protected $name = 'package:make-rule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new validation rule for the specified package.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the rule class.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub('/rule.stub', [
            'NAMESPACE' => $this->getClassNamespace($module),
            'CLASS' => $this->getFileName(),
        ]))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->getModulePath($this->getModuleName());

        $rulePath = GenerateConfigReader::read('rules');

        return $path.$rulePath->getPath().'/'.$this->getFileName().'.php';
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name'));
    }
}
