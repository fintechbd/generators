<?php

namespace Fintech\Generator\Exceptions;

use Exception;
use Throwable;


/**
* Class GeneratorException
* @package Fintech\Generator\Exceptions
*/
class GeneratorException extends Exception
{
    /**
     * CoreException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
}
