<?php

namespace Hermes\Core\Exceptions;

use Throwable;

class ResponseException extends Exception
{

    protected $code = self::RESPONSE_EXCEPTION;
    protected $statusCode;
    protected $response;

    public function __construct($response = null, $statusCode = 500, Throwable $previous = null)
    {
        $this->response = $response;
        $this->statusCode = $statusCode;

        $message = "[HTTP {$this->statusCode}] Failed request";
        if(!is_null($previous))
        {
            $message .= " with message {$previous->getMessage()}";
        }

        parent::__construct($message, $this->code, $previous);
    }

    public function hasResponse()
    {
        return !is_null($this->response);
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

}