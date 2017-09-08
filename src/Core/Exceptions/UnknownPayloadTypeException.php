<?php

namespace Hermes\Core\Exceptions;

class UnknownPayloadTypeException extends Exception
{

    protected $code = self::UNKNOWN_PAYLOAD_TYPE_EXCEPTION;

    /**
     * Request body payload type
     *
     * @var string
     */
    protected $payloadType;


    /**
     * UnknownPayloadTypeException constructor
     * @param string $payloadType
     * @param Throwable|null $previous
     */
    public function __construct($payloadType, $previous = null)
    {
        $this->payloadType = $payloadType;

        $message = "Unknown payload type '{$this->getPayloadType()}'";
        parent::__construct($message, $this->code, $previous);
    }

    /**
     * Get the payload type
     *
     * @return string
     */
    protected function getPayloadType()
    {
        return $this->payloadType;
    }

}