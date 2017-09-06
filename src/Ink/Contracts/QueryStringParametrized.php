<?php

namespace Hermes\Ink\Contracts;


interface QueryStringParametrized
{

    /**
     * Returns the currently active query string parameters
     *
     * @return array
     */
    public function getQueryStringParameters();

    /**
     * Returns the query string parameter if present
     *
     * @param string $parameter
     * @param string $default
     *
     * @return mixed
     */
    public function getQueryStringParameter($parameter, $default = null);


    /**
     * Checks if it has the specified query string parameter
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasQueryStringParameter($parameter);

    /**
     * Add the specified query string parameter
     *
     * @param string $key
     * @param mixed $value
     * @param string $keyName
     */
    public function addQueryStringParameter($key, $value, $keyName = null);

    /**
     * Builds the query string
     *
     * @return string
     */
    public function buildQueryString();


}