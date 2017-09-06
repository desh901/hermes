<?php

namespace Hermes\Ink\Contracts;

use GuzzleHttp\Psr7\Response as GuzzleResponse;

interface Response
{


    /**
     * Checks whether the request body is empty
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * Get the request unique ID
     *
     * @return string
     */
    public function getUUID();


    /**
     * Returns the request instance
     *
     * @return Request
     */
    public function getRequest();

    /**
     * Returns the Guzzle raw response
     *
     * @return GuzzleResponse
     */
    public function getRawResponse();

    /**
     * Returns the request execution time in milliseconds
     *
     * @return int
     */
    public function getExecutionTime();

    /**
     * Returns the raw request body
     *
     * @param bool $rewind
     *
     * @return string
     */
    public function getRawBody($rewind = false);

}