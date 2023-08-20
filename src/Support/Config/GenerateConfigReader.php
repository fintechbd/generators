<?php

namespace Fintech\Generator\Support\Config;

class GenerateConfigReader
{
    /**
     * @param string $value
     * @return GeneratorPath
     */
    public static function read(string $value): GeneratorPath
    {
        return new GeneratorPath(config("generators.paths.generator.{$value}"));
    }
}
