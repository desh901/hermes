<?php

namespace Hermes\Core\Exceptions;

use Symfony\Component\HttpFoundation\Request;

class CallbackVerificationFailed extends Exception
{

    protected $code = self::CALLBACK_VERIFICATION_FAILED;
    protected $request;

    public function __construct($message, Request $request)
    {
        $this->request = $request;

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