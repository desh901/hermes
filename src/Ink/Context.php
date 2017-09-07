<?php

namespace Hermes\Ink;

use Hermes\Ink\Contracts\Context as ContextContract;
use Hermes\Ink\Contracts\Credentials;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Str;

class Context implements ContextContract
{

    /**
     * Api request mode
     *
     * @var string
     */
    protected $mode;

    /**
     * Api base url
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Request timeout interval in seconds
     *
     * @var int
     */
    protected $timeout;

    /**
     * Cache instance
     *
     * @var Repository
     */
    protected $cache;

    /**
     * Callback verification mode active
     *
     * @var bool
     */
    protected $verifyCallbacks;

    /**
     * Credentials instance for the context
     *
     * @var Credentials
     */
    protected $credentials;


    public function __construct($config, Repository $cache, Credentials $credentials, $verifyCallbacks = false)
    {

        $this->mode = $config['mode'];
        $this->timeout = $config[$this->mode]['timeout'];
        $this->baseUrl = $config[$this->mode]['baseUrl'];
        $this->cache = $cache;
        $this->credentials = $credentials;
        $this->verifyCallbacks = $verifyCallbacks;

        if(!Str::endsWith($this->baseUrl, '/')) $this->baseUrl .= '/';

    }

    /**
     * Returns the base url that
     *
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Returns the request timeout in seconds
     *
     * @return integer
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Returns the library mode (e.g. 'live' or 'sandbox')
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Checks if the callback verification is enabled
     *
     * @return bool
     */
    public function verifyCallbacks()
    {
        return $this->verifyCallbacks;
    }

    /**
     * Return the credentials object
     *
     * @return Credentials
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * Return the cache instance
     *
     * @return Repository
     */
    public function getCache()
    {
        return $this->cache;
    }
}