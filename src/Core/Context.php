<?php

namespace Hermes\Core;

use Hermes\Core\Contracts\Credentials;
use Illuminate\Contracts\Cache\Repository;
use Hermes\Core\Contracts\Context as BaseContext;

class Context implements BaseContext
{

    /**
     * API interaction mode
     *
     * @var string
     */
    protected $mode;

    /**
     * Request timeout expressed in seconds
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
     * API Base url where to route requests
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Checks if callback verification is active
     *
     * @var boolean
     */
    protected $verifyCallbacks;

    /**
     * Credentials instance
     *
     * @var Credentials
     */
    protected $credentials;


    public function __construct($mode, $timeout, Repository $cache, $baseUrl, Credentials $credentials, $verifyCallbacks)
    {

        $this->mode = $mode;
        $this->timeout = $timeout;
        $this->cache = $cache;
        $this->baseUrl = $baseUrl;
        $this->credentials = $credentials;
        $this->verifyCallbacks = $verifyCallbacks;

    }

    /**
     * Returns the base url that
     *
     * @return string
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

}