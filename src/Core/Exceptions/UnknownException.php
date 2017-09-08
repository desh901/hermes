<?php

namespace Hermes\Core\Exceptions;

class UnknownException extends Exception
{

    protected $code = self::UNKNOWN_EXCEPTION;

    public function __construct($message, $previous = null)
    {
        parent::__construct($message, $this->code, $previous);
    }

}