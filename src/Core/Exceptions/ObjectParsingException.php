<?php

namespace Hermes\Core\Exceptions;

class ObjectParsingException extends Exception
{

    protected $code = self::OBJECT_PARSING_EXCEPTION;

    public function __construct($object, array $attributes, $previous = null)
    {
        $objectClass = is_object($object) ? get_class($object) : $object;
        $encodedAttributes = json_encode($attributes);

        $message = "Could not parse object '{$objectClass}' with attributes {$encodedAttributes}";
        parent::__construct($message, $this->code, $previous);
    }

}