<?php

namespace Hermes\Core\Exceptions;

use Throwable;

class EntityResolutionException extends Exception
{

    protected $code = self::ENTITY_RESOLUTION_EXCEPTION;

    public function __construct($entity, Throwable $previous = null)
    {
        $message = "Cannot instantiate action for entity '$entity'.";
        parent::__construct($message, $this->code, $previous);
    }

}