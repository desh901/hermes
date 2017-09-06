<?php

namespace Hermes\Core\Exceptions;

class RequestParametersValidationError extends ValidationException
{

    /**
     * Exception error code
     *
     * @var int
     */
    protected $code = self::REQUEST_PARAMETERS_VALIDATION_EXCEPTION;

    /**
     * Exception message
     * @var string
     */
    protected $message = "Request parameters validation failed due to the following errors: ";

}