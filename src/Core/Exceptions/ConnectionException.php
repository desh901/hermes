<?php

namespace Hermes\Core\Exceptions;

class ConnectionException extends Exception
{

    protected $code = self::CONNECTION_EXCEPTION;
    protected $statusCode;
    protected $response;

    public function __construct($response = null, $statusCode = 500, $previous = null)
    {
        $this->response = $response;
        $this->statusCode = $statusCode;

        $message = "[HTTP {$this->statusCode}] The server responded with an error.";

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