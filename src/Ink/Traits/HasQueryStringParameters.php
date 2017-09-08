<?php

namespace Hermes\Ink\Traits;


use Illuminate\Support\Arr;

trait HasQueryStringParameters
{


    /**
     * The query string parameters attributes
     *
     * @var array
     */
    protected $queryStringParameters = [];

    /**
     * Returns the currently active query string paramters
     *
     * @return array
     */
    public function getQueryStringParameters()
    {

        return $this->queryStringParameters;

    }

    /**
     * Returns the query string parameter if present
     *
     * @param string $parameter
     * @param string $default
     *
     * @return mixed
     */
    public function getQueryStringParameter($parameter, $default = null)
    {

        return Arr::get($this->queryStringParameters, $parameter, $default);

    }


    /**
     * Checks if it has the specified query string parameter
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasQueryStringParameter($parameter)
    {

        return in_array($parameter, $this->queryStringParameters, true);

    }

    /**
     * Add the specified query string parameter
     *
     * @param string $key
     * @param mixed $value
     * @param string $keyName
     */
    public function addQueryStringParameter($key, $value, $keyName = null)
    {

        if($this->hasQueryStringParameter($key)) {
            $this->queryStringParameters[$key] = Arr::wrap($this->queryStringParameters[$key]);
            $keyName ? $this->queryStringParameters[$key][$keyName] = $value
                : $this->queryStringParameters[$key][] = $value;
        }else {

            $keyName ? $this->queryStringParameters[$key] = [ $keyName => $value]
                : $this->queryStringParameters[$key] = $value;

        }

    }

    /**
     * Builds the query string
     *
     * @return string
     */
    public function buildQueryString()
    {
        $query = trim(http_build_query($this->getQueryStringParameters()));
        $query = preg_replace('/%5B[00-9]+%5D/simU', '%5B%5D', $query);
        return !empty($query) ? '?' . $query : '';
    }


}