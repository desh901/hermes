<?php

namespace Hermes\Core\Exceptions;

use Illuminate\Contracts\Support\MessageBag;

abstract class ValidationException extends Exception
{

    /**
     * Exception error code
     *
     * @var int
     */
    protected $code;

    /**
     * Exception message
     * @var string
     */
    protected $message = "Validation failed due to the following errors: ";

    /**
     * Error bag messages
     *
     * @var MessageBag
     */
    protected $errors;

    public function __construct(MessageBag $errors)
    {

        $this->message .= $errors->toJson();

        parent::__construct($this->message, $this->code);
    }

    /**
     * Returns the validation error messages
     *
     * @return MessageBag
     */
    public function getErrors()
    {

        return $this->errors;

    }

}