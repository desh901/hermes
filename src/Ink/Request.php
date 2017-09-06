<?php

namespace Hermes\Ink;

use GuzzleHttp\HandlerStack;
use Hermes\Ink\Contracts\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\RequestInterface;
use Hermes\Core\Exceptions\UnknownException;
use Hermes\Core\Exceptions\ResponseException;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Hermes\Core\Exceptions\ConnectionException;
use Hermes\Ink\Contracts\Request as RequestContract;
use Hermes\Core\Exceptions\UnknownPayloadTypeException;

class Request implements RequestContract
{


    /**
     * Guzzle client
     *
     * @var GuzzleClient
     */
    protected $client;


    /**
     * The request base URL
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * The request timeout
     *
     * @var int
     */
    protected $timeout;

    /**
     * The request payload type
     *
     * @var string
     */
    protected $payloadType;

    /**
     * Request method
     *
     * @var string
     */
    protected $method;

    /**
     * The request endpoint
     *
     * @var string
     */
    protected $endpoint;

    /**
     * Request headers
     *
     * @var array
     */
    protected $headers;

    /**
     * Request payload
     *
     * @var mixed
     */
    protected $payload;

    /**
     * Authentication to apply to the internal request
     *
     * @var \Closure
     */
    protected $authentication;


    /**
     * Request constructor.
     *
     * @param $baseUrl
     * @param $timeout
     * @param $payloadType
     * @param $method
     * @param $endpoint
     * @param array $headers
     * @param $payload
     * @param \Closure $authentication
     */
    public function __construct($baseUrl, $timeout, $payloadType, $method, $endpoint, array $headers, $payload, \Closure $authentication)
    {
        $this->baseUrl = $baseUrl;
        $this->timeout = $timeout;
        $this->payloadType = $payloadType;
        $this->method = $method;
        $this->endpoint = $endpoint;
        $this->headers = $headers;
        $this->payload = $payload;
        $this->authentication = $authentication;
    }

    /**
     * Build the request headers
     *
     * @return array
     */
    protected function buildHeaders()
    {
        return $this->headers;
    }

    /**
     * Builds the request body
     *
     * @param array $options
     *
     * @throws
     */
    protected function buildBody(array &$options)
    {

        if(empty($this->payload)){
            return;
        }

        switch($this->payloadType){
            case 'json':
            case 'raw':
            case 'form_params':
            case 'multipart':
                $options[$this->payloadType] = $this->payload;
                break;

            default:
                throw new UnknownPayloadTypeException($this->payloadType);
        }

    }

    /**
     * Builds the authentication for the guzzle request
     *
     * @return \Closure
     */
    protected function buildAuthentication()
    {

        $authenticationHandler = $this->authentication;
        return function($handler) use($authenticationHandler){
            return function (RequestInterface $request, array $options) use($handler, $authenticationHandler) {
                $request = $authenticationHandler($request);
                return $handler($request, $options);
            };
        };

    }


    /**
     * Builds the client needed to send the request.
     *
     * @return GuzzleClient
     */
    protected function buildClient()
    {
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());
        $stack->push($this->buildAuthentication());

        $options = [
            'timeout'  => $this->timeout,
            'base_uri' => $this->baseUrl,
            'headers' => $this->buildHeaders(),
            'handler' => $stack,
            'http_errors' => false
        ];
        $this->buildBody($options);

        return new GuzzleClient($options);
    }

    /**
     * Executes the request
     *
     * @return Response
     * @throws UnknownException
     */
    public function run()
    {
        $this->client = $this->buildClient();
        $startTime = round(microtime(true) * 1000);

        try {
            $response = $this->client->request($this->method, $this->endpoint);
            $this->checkStatus($response);

        } catch (\Exception $e) {
            if(!$e instanceof UnknownException) {
                throw new UnknownException($e->getMessage(), $e);
            }

            throw $e;
        }

        $endTime = round(microtime(true) * 1000);
        $executionTime = $endTime - $startTime;

        return new \Hermes\Ink\Response($this, $response, $executionTime);
    }

    /**
     *
     * Check if the response is successful
     *
     * @param GuzzleResponse $response
     * @throws ConnectionException
     * @throws ResponseException
     */
    private function checkStatus(GuzzleResponse $response)
    {
        $statusCode = $response->getStatusCode();
        if($statusCode >= 400 && $statusCode < 500){
            throw new ResponseException($response, $statusCode);
        }else if($statusCode >= 500){
            throw new ConnectionException($response, $statusCode);
        }
    }

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
    )
    {

        $requestMock = \Mockery::mock(self::class , [
            $baseUrl, $timeout, $payloadType,
            $method, $endpoint, $headers, $payload,
            $authentication
        ])->makePartial();

        $requestMock->shouldAllowMockingProtectedMethods()
            ->shouldReceive('buildClient')
            ->andReturnUsing(function() use($requestMock, $statusCode, $fakeBody){

                $headers = [
                    'Content-Type' => 'application/json',
                    'X-Api-Version' => 'v1.1'
                ];

                $mock = new MockHandler([
                    new GuzzleResponse($statusCode, $headers, $fakeBody)
                ]);

                $stack = HandlerStack::create($mock);
                $options = [
                    'timeout' => $requestMock->timeout,
                    'base_uri' => $requestMock->baseUrl,
                    'headers' => $requestMock->buildHeaders(),
                    'handler' => $stack,
                    'http_errors' => false
                ];
                $requestMock->buildBody($options);

                return new GuzzleClient($options);
            });

        return $requestMock->makePartial();
    }



}