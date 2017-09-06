<?php

namespace Hermes\Ink\Contracts;

interface Action
{


    /**
     * Return a fake instance of an action for testing purposes
     *
     * @param Context $context
     * @param integer $statusCode
     * @param string $body
     * @return mixed
     */
    public static function fake(Context $context, $statusCode, $body);

    /**
     * Check whether the action has an associated response
     *
     * @return bool
     */
    public function hasResponse();

    /**
     * Returns the original response
     *
     * @return Response
     */
    public function getResponse();


    /**
     * Sends the request to the specified endpoint
     */
    public function send();


}