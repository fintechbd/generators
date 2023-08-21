<?php

namespace Fintech\Generator\Exceptions;


/**
 * Class FileAlreadyExistException
 * @package Fintech\Generator\Exceptions
 */
class FileAlreadyExistException extends \Exception
{
    public function __construct($message = '', $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
