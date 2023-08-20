<?php

namespace Fintech\Generator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Fintech\Generator\Generator
 */
class Generator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Fintech\Generator\Generator::class;
    }
}
