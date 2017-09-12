<?php

namespace Hermes\Ink\Contracts\Callbacks;


interface Callback
{

    /**
     * Generic callback parser get the raw data and parse it to a provided callback type
     *
     * @param array $payload
     * @return \Hermes\Ink\Callback
     */
    public static function parse(array $payload);

    /**
     * Detect if the received payload contains a callback of this type
     *
     * @param array $payload
     * @return bool
     */
    public static function isInstance(array $payload);

}