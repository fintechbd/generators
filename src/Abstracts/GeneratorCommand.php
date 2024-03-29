<?php

namespace Fintech\Generator\Abstracts;

use Fintech\Generator\Exceptions\FileAlreadyExistException;
use Fintech\Generator\Exceptions\GeneratorException;
use Fintech\Generator\Generators\FileGenerator;
use Illuminate\Console\Command;
use InvalidArgumentException;

abstract class GeneratorCommand extends Command
{
    /**
     * The name of 'name' argument.
     *
     * @var string
     */
    protected $argumentName = '';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {

        $path = str_replace('\\', '/', $this->getDestinationFilePath());

        if (!$this->laravel['files']->isDirectory($dir = dirname($path))) {
            $this->laravel['files']->makeDirectory($dir, 0777, true);
        }

        $contents = $this->getTemplateContents();

        try {
            $this->components->task("Generating file {$path}", function () use ($path, $contents) {
                $overwriteFile = $this->hasOption('force') ? $this->option('force') : false;
                (new FileGenerator($path, $contents))->withFileOverwrite($overwriteFile)->generate();
            });

            return self::SUCCESS;

        } catch (FileAlreadyExistException $e) {
            $this->components->error("File : {$path} already exists.");

            return self::FAILURE;
        }

        return self::FAILURE;
    }

    /**
     * Get the destination file path.
     *
     * @return string
     */
    abstract protected function getDestinationFilePath();

    /**
     * Get template contents.
     *
     * @return string
     */
    abstract protected function getTemplateContents();

    /**
     * Get class namespace.
     *
     * @param string $module
     * @return string
     *
     * @throws GeneratorException
     */
    public function getClassNamespace($module)
    {
        $extra = str_replace($this->getClass(), '', $this->argument($this->argumentName));

        $extra = str_replace('/', '\\', $extra);

        $namespace = config('fintech.generators.namespace');

        $namespace .= '\\' . $module;

        $namespace .= '\\' . $this->getDefaultNamespace();

        $namespace .= '\\' . $extra;

        $namespace = str_replace('/', '\\', $namespace);

        return trim($namespace, '\\');
    }

    /**
     * Get class name.
     *
     * @return string
     */
    public function getClass()
    {
        return class_basename($this->argument($this->argumentName));
    }

    /**
     * Get default namespace.
     *
     * @param null $type
     *
     * @throws GeneratorException
     */
    public function getDefaultNamespace($type = null): string
    {
        if (!$type) {
            if (property_exists($this, 'type')) {
                $type = $this->type;
            }
        }

        if (!$type) {
            throw new GeneratorException('Stub type argument or property is not configured.');
        }

        if (!config("fintech.generators.paths.generator.{$type}")) {
            throw new InvalidArgumentException("Generator is missing [{$type}] config, check generators.php file.");
        }

        $config = config("fintech.generators.paths.generator.{$type}");

        return $config['namespace'] ?? $config['path'];

    }
}
