<?php

namespace Hermes\Ink\Contracts;

use Hermes\Core\Exceptions\UnknownException;

interface Request
{

    /**
     *
     * Creates a mock of the request object for testing purposes
     *
     * @param int $statusCode
     * @param string $fakeBody
     * @param string $baseUrl
     * @param int $timeout
     * @param string $payloadType
     * @param string $method
     * @param string $endpoint
     * @param array $headers
     * @param mixed $payload
     * @param \Closure $authentication
     *
     * @return \Mockery\Mock
     */
    public static function fake(
        $statusCode, $fakeBody,
        $baseUrl, $timeout, $payloadType,
        $method, $endpoint, array $headers,
        $payload, \Closure $authentication
    );

    /**
     * Executes the request
     *
     * @return Response
     * @throws UnknownException
     */
    public function run();

}