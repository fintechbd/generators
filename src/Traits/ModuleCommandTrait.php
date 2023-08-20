<?php

namespace Fintech\Generator\Traits;

use Fintech\Generator\Exceptions\GeneratorException;

trait ModuleCommandTrait
{
    /**
     * Get the module name.
     *
     * @return string
     *
     * @throws GeneratorException
     */
    public function getModuleName()
    {
        $fallbackPath = storage_path('cli-package.json');

        $module = $this->argument('module');

        if (! $module && file_exists($fallbackPath)) {

            $fallback = json_decode(file_get_contents($fallbackPath), true);

            if ($fallback['use']) {

                $module = $fallback['use'] ?? null;
            }
        }

        if (! $module) {
            throw new GeneratorException('Invalid or Missing module name on argument.');
        }

        return $module;
    }

    public function getModulePath(string $module)
    {
        $rootPath = config('generators.paths.modules');

        return $rootPath.'/'.$module.'/';
    }
}
