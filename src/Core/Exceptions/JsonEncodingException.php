<?php

namespace Hermes\Core\Exceptions;

class JsonEncodingException extends Exception
{

    /**
     * Create a new JSON encoding exception for the object.
     *
     * @param  mixed  $object
     * @param  string  $message
     * @return static
     */
    public static function forObject($object, $message)
    {
        $message = 'Error encoding model ['.get_class($object).'] to JSON: '.$message;
        return new static($message, self::JSON_ENCODING_EXCEPTION);
    }

    /**
     * Create a new JSON encoding exception for an attribute.
     *
     * @param  mixed  $object
     * @param  mixed  $key
     * @param  string $message
     * @return static
     */
    public static function forAttribute($object, $key, $message)
    {
        $class = get_class($object);
        $message = "Unable to encode attribute [{$key}] for model [{$class}] to JSON: {$message}.";

        return new static($message, self::JSON_ENCODING_EXCEPTION);
    }
}
