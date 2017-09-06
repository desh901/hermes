<?php

namespace Hermes\Core\Exceptions;

class ResponseParametersValidationError extends ValidationException
{

    /**
     * Exception error code
     *
     * @var int
     */
    protected $code = self::RESPONSE_PARAMETERS_VALIDATION_EXCEPTION;

    /**
     * Exception message
     * @var string
     */
    protected $message = "Response parameters validation failed due to the following errors: ";

}