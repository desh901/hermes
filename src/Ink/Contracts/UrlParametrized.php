<?php

namespace Hermes\Ink\Contracts;


use Hermes\Core\Exceptions\UrlParametersValidationError;

interface UrlParametrized
{


    /**
     * Validates url parameters provided using given rules
     *
     * @param array $parameters
     * @param array $mergeRules
     *
     * @throws UrlParametersValidationError
     */
    public function validateRequestParameters(array $parameters, array $mergeRules = []);

    /**
     * Returns all the current specified url parameters
     *
     * @return array
     */
    public function getUrlParameters();

    /**
     * Returns the url parameter if present
     *
     * @param string $parameter
     * @param string $default
     *
     * @return mixed
     */
    public function getUrlParameter($parameter, $default = null);


    /**
     * Checks if it has the specified url parameter
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasUrlParameter($parameter);

    /**
     * Set the specified request parameter
     *
     * @param string $key
     * @param mixed $value
     */
    public function setUrlParameter($key, $value);


}