<?php

namespace Hermes\Ink;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Hermes\Ink\Contracts\Response as ResponseContract;

class Response implements ResponseContract
{

    /**
     * Unique identifier of the current request
     *
     * @var string
     */
    protected $uuid;

    /**
     * Associated request
     *
     * @var Request
     */
    protected $request;

    /**
     * Raw Guzzle response
     *
     * @var GuzzleResponse
     */
    protected $rawResponse;

    /**
     * Execution time
     *
     * @var int
     */
    protected $executionTime;

    /**
     * Response constructor.
     *
     * @param Request $request
     * @param GuzzleResponse $response
     * @param $executionTime
     */
    public function __construct(Request $request, GuzzleResponse $response, $executionTime)
    {

        $this->request = $request;
        $this->rawResponse = $response;
        $this->executionTime = $executionTime;

    }

    /**
     * Checks whether the request body is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return ($this->getRawResponse()->getStatusCode() === 204
            || $this->getRawResponse()->getStatusCode() === 304);
    }

    /**
     * Get the request unique ID
     *
     * @return string
     */
    public function getUUID()
    {
        return $this->uuid;
    }


    /**
     * Returns the request instance
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the Guzzle raw response
     *
     * @return GuzzleResponse
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    /**
     * Returns the request execution time in milliseconds
     *
     * @return int
     */
    public function getExecutionTime()
    {
        return $this->executionTime;
    }

    /**
     * Returns the raw request body
     *
     * @param bool $rewind
     *
     * @return bool|string
     */
    public function getRawBody($rewind = false)
    {
        if($rewind) {
            $this->rawResponse->getBody()->rewind();
        }

        return $this->rawResponse->getBody()->getContents();
    }

}