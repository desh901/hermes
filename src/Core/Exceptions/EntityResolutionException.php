<?php

namespace Hermes\Core\Exceptions;

class EntityResolutionException extends Exception
{

    protected $code = self::ENTITY_RESOLUTION_EXCEPTION;

    public function __construct($entity, $previous = null)
    {
        $message = "Cannot instantiate action for entity '$entity'.";
        parent::__construct($message, $this->code, $previous);
    }

}