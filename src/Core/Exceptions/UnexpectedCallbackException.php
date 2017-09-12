<?php

namespace Hermes\Core\Exceptions;

use Symfony\Component\HttpFoundation\Request;

class UnexpectedCallbackException extends Exception
{

    protected $code = self::UNEXPECTED_CALLBACK_EXCEPTION;
    protected $request;

    public function __construct($payload, Request $request)
    {
        $this->request = $request;

        $message = 'Unexpected callback received: ' . ($payload ? json_encode($payload) : '(null)');

        parent::__construct($message, $this->code);
    }

    public function hasRequest()
    {
        return !is_null($this->request);
    }

    public function getRequest()
    {
        return $this->request;
    }

}