<?php

namespace Hermes\Core\Exceptions;

use Throwable;

class ObjectParsingException extends Exception
{

    protected $code = self::OBJECT_PARSING_EXCEPTION;

    public function __construct($object,array $attributes, Throwable $previous = null)
    {
        $objectClass = get_class($object);
        $encodedAttributes = json_encode($attributes);

        $message = "Could not parse object '{$objectClass}' with attributes {$encodedAttributes}";
        parent::__construct($message, $this->code, $previous);
    }

}