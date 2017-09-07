<?php

namespace Hermes\Ink;

use Illuminate\Support\Arr;
use Mockery\Mock;
use Illuminate\Support\Str;
use Hermes\Ink\Contracts\Context;
use Hermes\Ink\Contracts\Response;
use Hermes\Ink\Contracts\Parametrized;
use Hermes\Ink\Traits\HasUrlParameters;
use Hermes\Ink\Contracts\UrlParametrized;
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
     * Action name
     *
     * @var $name
     */
    protected $name;

    /**
     * Action request method
     *
     * @var string
     */
    protected $method;

    /**
     * Action request endpoint
     *
     * @var string
     */
    protected $uri;

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
     * Action base url
     *
     * @var string
     */
    protected $baseUrl;


    /**
     * Action constructor
     *
     * @param Context $context
     * @param Validation $validator
     * @param Parsing $parser
     */
    public function __construct(Context $context, Validation $validator, Parsing $parser)
    {

        $this->context = $context;
        $this->validator = $validator;
        $this->parser = $parser;
        $this->baseUrl = $this->context->getBaseUrl();

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
     * Returns the action base url
     *
     * @return string
     */
    public function getBaseUrl()
    {

        return $this->baseUrl;

    }

    /**
     * Set the action base url
     *
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {

        $this->baseUrl = Str::endsWith($baseUrl, '/') ? $baseUrl : $baseUrl.'/';

    }

    /**
     * Get the action name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the action name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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

    /**
     * Define the authentication function to apply to the request
     *
     * @param GuzzleRequest $request
     * @return GuzzleRequest $request
     */
    protected function authenticate(GuzzleRequest $request)
    {

        return $this->context->getCredentials()->apply($request);

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
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set the request method
     *
     * @param string $method
     * @return self
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Get the base request endpoint
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->uri;
    }

    /**
     * Set the base request endpoint
     *
     * @param string $uri
     * @return self
     */
    public function setEndpoint($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * Sets the action options like base url, timeout etc.
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {

        $this->setBaseUrl(Arr::get($options, 'base_url', $this->getBaseUrl()));
        $this->setName(Arr::get($options, 'name', $this->getName()));
        $this->setName(Arr::get($options, 'as', $this->getName()));
        $this->setMethod(Arr::get($options,'method', $this->getMethod()));

    }

    /**
     * Get the action current options
     *
     * @return array $options
     */
    public function getOptions()
    {

        return [
            'base_url' => $this->getBaseUrl(),
            'name' => $this->getName(),
            'as' => $this->getName(),
            'method' => $this->getMethod()
        ];

    }

    /**
     * Headers to include into the request
     *
     * @return array
     */
    protected abstract function getHeaders();

    /**
     * Parse the received response to matching objects
     *
     * @param array $parsedResponse
     *
     * @return mixed
     */
    protected abstract function parseResponse(array $parsedResponse);

}