<?php

namespace Hermes\Core\Exceptions;

class UrlParametersValidationError extends ValidationException
{

    /**
     * Exception error code
     *
     * @var int
     */
    protected $code = self::URL_PARAMETERS_VALIDATION_EXCEPTION;

    /**
     * Exception message
     * @var string
     */
    protected $message = "Url parameters validation failed due to the following errors: ";

}