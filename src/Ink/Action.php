<?php

namespace Hermes\Ink;

use Mockery\Mock;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Hermes\Ink\Contracts\Context;
use Hermes\Ink\Contracts\Response;
use Hermes\Core\Exceptions\Exception;
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
     * Action's request timeout
     *
     * @var int
     */
    protected $timeout;

    /**
     * Parse with specified objects
     *
     * @var array
     */
    protected $parseWith = [];



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
        $this->timeout = $this->context->getTimeout();

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
            $this->getBaseUrl(),
            $this->getTimeout(),
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
        if($validator->fails()){
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
     * Add or change the action name.
     *
     * @param  string  $name
     * @return $this
     */
    public function name($name)
    {
        $this->name = isset($this->name) ? $this->name.'.'.$name : $name;

        return $this;
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
    public abstract function getPayloadType();

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
     * Get the base request timeout in seconds
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Set the base request timeout in seconds
     *
     * @param int $timeout
     * @return self
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Sets the action options like base url, timeout etc.
     *
     * @param array $options
     * @return self
     */
    public function setOptions(array $options)
    {

        $this->setBaseUrl(Arr::get($options, 'base_url', $this->getBaseUrl()));
        $this->setName(Arr::get($options, 'as', $this->getName()));
        $this->setMethod(Arr::get($options,'method', $this->getMethod()));
        $this->setTimeout(Arr::get($options, 'timeout', $this->getTimeout()));

        return $this;

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
     * Add an object parser to the stack
     *
     * @param string $class
     * @param string $itemKey
     * @throws Exception
     * @return self
     */
    public function parseWith($class, $itemKey = null)
    {

        if(!class_exists($class)) throw new Exception("Object class '{$class}' not found.");

        $this->parseWith[] = [
            'class' => $class,
            'itemKey' => $itemKey
        ];

        return $this;
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
    protected function parseResponse(array $parsedResponse)
    {

        if(empty($this->parseWith)) return $parsedResponse;

        $results = [];

        foreach($this->parseWith as $objectParser)
        {
            $key = $this->getKeyForResult($objectParser);
            $results[$key] = $this->parseObject($objectParser, $parsedResponse);
        }

        if(count($results) === 1) return head($results);

        return (object) $results;
    }

    /**
     * Get the results array key for the response
     *
     * @param array $parser
     * @return string
     */
    protected function getKeyForResult(array $parser)
    {

        if(Arr::has($parser, 'itemKey') && !is_null($parser['itemKey'])) {

            return $parser['itemKey'];

        }

        return Str::snake(class_basename($parser['class']));

    }

    /**
     * Parse and object from the object parser
     *
     * @param array $parser
     * @param array $data
     *
     * @return Object
     */
    protected function parseObject($parser, $data)
    {

        $objectClass = $parser['class'];

        return $objectClass::create($data, $parser['itemKey']);

    }

    /**
     * Get the current object parsers
     *
     * @return array
     */
    public function getObjectParsers()
    {
        return $this->parseWith;
    }

    /**
     * Prepare the action for the serialization
     */
    public function prepareForSerialization()
    {

        unset($this->validator, $this->context, $this->parser);

    }

    public function __wakeup()
    {
        $this->context = app(Context::class);
        $this->validator = app(Validation::class);
        $this->parser = app(Parsing::class);
    }


}