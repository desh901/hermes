<?php

namespace Hermes\Core\Exceptions;

use Throwable;

class UnknownException extends Exception
{

    protected $code = self::UNKNOWN_EXCEPTION;

    public function __construct($message, Throwable $previous = null)
    {
        parent::__construct($message, $this->code, $previous);
    }

}