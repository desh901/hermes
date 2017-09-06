<?php

namespace Hermes\Core\Exceptions;

use \Exception as BaseException;

class Exception extends BaseException
{

    const UNKNOWN_EXCEPTION = 0;
    const ENTITY_RESOLUTION_EXCEPTION = 1;
    const RESPONSE_EXCEPTION = 2;
    const URL_PARAMETERS_VALIDATION_EXCEPTION = 3;
    const REQUEST_PARAMETERS_VALIDATION_EXCEPTION = 4;
    const RESPONSE_PARAMETERS_VALIDATION_EXCEPTION = 5;
    const UNKNOWN_PAYLOAD_TYPE_EXCEPTION = 6;
    const CONNECTION_EXCEPTION = 7;
    const JSON_ENCODING_EXCEPTION = 8;

}