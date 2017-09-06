<?php

namespace Hermes\Ink\Contracts;

use Hermes\Core\Exceptions\RequestParametersValidationError;

interface Parametrized
{

    /**
     * Validates the request parameters provided using given rules
     *
     * @param array $parameters
     * @param array $mergeRules
     *
     * @throws RequestParametersValidationError
     */
    public function validateRequestParameters(array $parameters, array $mergeRules = []);


    /**
     * Returns the current request parameters
     *
     * @return array
     */
    public function getRequestParameters();

    /**
     * Returns the request parameter if present
     *
     * @param string $parameter
     * @param string $default
     *
     * @return mixed
     */
    public function getRequestParameter($parameter, $default = null);


    /**
     * Checks if it has the specified request parameter
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasRequestParameter($parameter);

    /**
     * Set the specified request parameter
     *
     * @param string $key
     * @param mixed $value
     */
    public function setRequestParameter($key, $value);


}