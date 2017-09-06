<?php

namespace Hermes\Ink\Traits;


use Hermes\Core\Exceptions\RequestParametersValidationError;
use Illuminate\Support\Arr;

trait HasRequestParameters
{

    /**
     * The request parameters attributes
     *
     * @var array
     */
    protected $requestParameters = [];

    /**
     * The request parameters rules
     *
     * @var array
     */
    protected $requestParametersRules = [];


    /**
     * Validates the request parameters provided using given rules
     *
     * @param array $parameters
     * @param array $mergeRules
     *
     * @throws RequestParametersValidationError
     */
    public function validateRequestParameters(array $parameters, array $mergeRules = [])
    {

        $rules = array_merge($this->requestParametersRules, $mergeRules);
        $validator = $this->validator->make($parameters, $rules);

        if($validator && $validator->fails())
            throw new RequestParametersValidationError($validator->getMessageBag());
    }

     /**
     * Returns the current request parameters
     *
     * @return array
     */
    public function getRequestParameters()
    {
        return $this->requestParameters;
    }

    /**
     * Returns the request parameter if present
     *
     * @param string $parameter
     * @param string $default
     *
     * @return mixed
     */
    public function getRequestParameter($parameter, $default = null)
    {

        return Arr::get($this->requestParameters, $parameter, $default);

    }


    /**
     * Checks if it has the specified request parameter
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasRequestParameter($parameter)
    {

        return Arr::has($this->requestParameters, $parameter);

    }

    /**
     * Set the specified request parameter
     *
     * @param string $key
     * @param mixed $value
     */
    public function setRequestParameter($key, $value)
    {

        Arr::set($this->requestParameters, $key, $value);

    }

}