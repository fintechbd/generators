<?php

namespace Fintech\Generator\Commands;

use Illuminate\Console\Command;

class GeneratorCommand extends Command
{
    public $signature = 'generators';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
