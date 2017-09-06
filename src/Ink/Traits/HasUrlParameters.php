<?php

namespace Hermes\Ink\Traits;


use Hermes\Core\Exceptions\UrlParametersValidationError;
use Illuminate\Support\Arr;

trait HasUrlParameters
{

    /**
     * The url parameters attributes
     *
     * @var array
     */
    protected $urlParameters = [];

    /**
     * Url parameters rules for validation
     *
     * @var array
     */
    protected $urlParametersRules = [];

    /**
     * Validates the request parameters provided using given rules
     *
     * @param array $parameters
     * @param array $mergeRules
     *
     * @throws UrlParametersValidationError
     */
    public function validateUrlParameters(array $parameters, array $mergeRules = [])
    {

        $rules = array_merge($this->urlParametersRules, $mergeRules);
        $validator = $this->validator->make($parameters, $rules);

        if ($validator && $validator->fails())
            throw new UrlParametersValidationError($validator->getMessageBag());

    }

    /**
     * Returns all the current specified url parameters
     *
     * @return array
     */
    public function getUrlParameters()
    {
        return $this->urlParameters;
    }

    /**
     * Returns the url parameter if present
     *
     * @param string $parameter
     * @param string $default
     *
     * @return mixed
     */
    public function getUrlParameter($parameter, $default = null)
    {

        return Arr::get($this->urlParameters, $parameter, $default);

    }


    /**
     * Checks if it has the specified url parameter
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasUrlParameter($parameter)
    {

        return Arr::has($this->urlParameters, $parameter);

    }

    /**
     * Set the specified request parameter
     *
     * @param string $key
     * @param mixed $value
     */
    public function setUrlParameter($key, $value)
    {

        Arr::set($this->urlParameters, $key, $value);

    }

}