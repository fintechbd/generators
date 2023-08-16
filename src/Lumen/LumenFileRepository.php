<?php

namespace Fintech\Generator\Lumen;

use Fintech\Generator\FileRepository;

class LumenFileRepository extends FileRepository
{
    /**
     * {@inheritdoc}
     */
    protected function createModule(...$args)
    {
        return new Module(...$args);
    }
}
