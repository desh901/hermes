<?php

namespace Hermes\Ink;

use Mockery\Mock;
use Hermes\Ink\Contracts\Context;
use Hermes\Ink\Contracts\Response;
use Hermes\Ink\Contracts\Parametrized;
use Hermes\Ink\Traits\HasUrlParameters;
use Hermes\Ink\Contracts\UrlParametrized;
use Illuminate\Contracts\Cache\Repository;
use Hermes\Ink\Traits\HasRequestParameters;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Hermes\Core\Exceptions\ResponseException;
use Hermes\Ink\Traits\HasQueryStringParameters;
use Hermes\Ink\Contracts\QueryStringParametrized;
use Hermes\Ink\Contracts\Action as ActionContract;
use Hermes\Core\Contracts\Parsing\Factory as Parsing;
use Hermes\Core\Exceptions\UrlParametersValidationError;
use \Illuminate\Contracts\Validation\Factory as Validation;
use Hermes\Core\Exceptions\RequestParametersValidationError;
use Hermes\Core\Exceptions\ResponseParametersValidationError;

abstract class Action implements ActionContract, Parametrized, UrlParametrized, QueryStringParametrized
{

    use HasQueryStringParameters,
        HasRequestParameters,
        HasUrlParameters;

    /**
     * Cache instance
     *
     * @var Repository
     */
    protected $cache;

    /**
     * Validation factory instance
     *
     * @var Validation
     */
    protected $validator;

    /**
     * Api context instance
     *
     * @var Context
     */
    protected $context;

    /**
     * Original response instance
     *
     * @var Response
     */
    protected $response;

    /**
     * Parser factory
     *
     * @var Parsing
     */
    protected $parser;

    /**
     * Response body validation rules
     *
     * @var array
     */
    protected $responseParametersRules = [];

    /**
     * Action constructor
     *
     * @param Context $context
     * @param Repository $cache
     * @param Validation $validator
     * @param Parsing $parser
     */
    public function __construct(Context $context, Repository $cache, Validation $validator, Parsing $parser)
    {

        $this->context = $context;
        $this->cache = $cache;
        $this->validator = $validator;
        $this->parser = $parser;

    }

    /**
     * Return a fake instance of an action for testing purposes
     *
     * @param Context $context
     * @param integer $statusCode
     * @param string $body
     * @return mixed
     */
    public static function fake(Context $context, $statusCode, $body)
    {
        $mock = \Mockery::mock(static::class, [$context])->makePartial();

        $mock->shouldAllowMockingProtectedMethods()
            ->shouldReceive('makeRequest')
            ->andReturn($mock->makeRequestFake($statusCode, $body));

        $mock->shouldReceive('authenticate')
            ->andReturnUsing(function(GuzzleRequest $request) {
                return $request;
            });

        return $mock;
    }

    /**
     * Prepare the fake request
     * @param int $statusCode
     * @param string $responseBody
     *
     * @return Mock
     */
    protected function makeRequestFake($statusCode, $responseBody){

        return Request::fake(
            $statusCode,
            $responseBody,
            $this->context->getBaseUrl(),
            $this->context->getTimeout(),
            $this->getPayloadType(),
            $this->getMethod(),
            $this->buildURI(),
            $this->getHeaders(),
            $this->buildPayload(),
            $this->getAuthenticationClosure()
        );

    }

    /**
     * Prepare the request
     *
     * @return Request
     */
    protected function makeRequest()
    {
        return (new Request(
            $this->context->getBaseUrl(),
            $this->context->getTimeout(),
            $this->getPayloadType(),
            $this->getMethod(),
            $this->buildURI(),
            $this->getHeaders(),
            $this->buildPayload(),
            $this->getAuthenticationClosure()
        ));
    }

    /**
     * Check whether the action has an associated response
     *
     * @return bool
     */
    public function hasResponse()
    {
        return !is_null($this->response);
    }

    /**
     * Returns the original response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Validates the request before sending it
     *
     * @throws UrlParametersValidationError|RequestParametersValidationError
     */
    protected function validateRequest()
    {

        $this->validateUrlParameters($this->urlParameters);
        $this->validateRequestParameters($this->requestParameters);

    }

    /**
     * Validates the response before parsing it
     *
     * @param array $responseBody;
     *
     * @throws ResponseParametersValidationError
     */
    protected function validateResponse(array $responseBody)
    {

        $validator = $this->validator->make($responseBody, $this->responseParametersRules);
        if($this->validator->fails()){
            throw new ResponseParametersValidationError($validator->getMessageBag());
        }

    }

    /**
     * Builds the final URI
     *
     * @return string
     */
    protected function buildURI()
    {

        $url = $this->getEndpoint();
        foreach($this->getUrlParameters() as $urlParameter => $value)
        {
            $url = str_replace('{' . $urlParameter . '}', $value, $url);
        }

        return $url . $this->buildQueryString();

    }

    /**
     * Builds the request payload
     *
     * @return array
     */
    protected function buildPayload()
    {
        return $this->getRequestParameters();
    }

    /**
     * Get the authentication closure to apply to the request
     *
     * @return \Closure
     */
    protected function getAuthenticationClosure()
    {
        return function(GuzzleRequest $request) {
            return $this->authenticate($request);
        };
    }


    /**
     * Sends the request to the specified endpoint
     */
    public function send()
    {

        $this->validateRequest();

        try {
            $this->response = $this->makeRequest()->run();
        }catch(ResponseException $exception) {
            // TODO: understand how to handle exceptions
        }

        $parsedResponse = $this->parseRawResponse($this->response);

        $this->validateResponse($parsedResponse);

        // TODO: see if object generation can be abstracted
        return $this->parseResponse($parsedResponse);
    }

    /**
     * Parse the response body
     *
     * @param Response $response
     *
     * @return array
     */
    protected function parseRawResponse(Response $response)
    {

        if(!$response->isEmpty())
        {
            return $this->parseRawBody(
                $response->getRawResponse()->getHeaderLine('Content-Type'),
                $response->getRawBody()
            );
        }

        return [];
    }

    /**
     * That function must return an array, to be validated against response_params_rules.
     *
     * @param $mimeType
     * @param $body
     *
     * @return mixed
     */
    protected function parseRawBody($mimeType, $body)
    {
        return $this->parser->parserFor($mimeType)->parse($body);
    }

    /*
     * Abstract methods
     */

    /**
     * Returns the payload mime type string
     * Supported:
     * - raw
     * - form_params
     * - multipart
     * - json
     *
     * @return string
     */
    protected abstract function getPayloadType();

    /**
     * Get the request method
     *
     * @return string
     */
    protected abstract function getMethod();

    /**
     * Get the base request endpoint
     *
     * @return string
     */
    protected abstract function getEndpoint();

    /**
     * Headers to include into the request
     *
     * @return array
     */
    protected abstract function getHeaders();

    /**
     * Define the authentication function to apply to the request
     *
     * @param GuzzleRequest $request
     */
    protected abstract function authenticate(GuzzleRequest $request);

    /**
     * Parse the received response to matching objects
     *
     * @param array $parsedResponse
     *
     * @return mixed
     */
    protected abstract function parseResponse(array $parsedResponse);

}