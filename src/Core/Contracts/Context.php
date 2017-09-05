<?php

namespace Hermes\Core\Contracts;


interface Context
{

    /**
     * Returns the base url that
     *
     * @return mixed
     */
    public function getBaseUrl();

    /**
     * Returns the request timeout in seconds
     *
     * @return integer
     */
    public function getTimeout();

    /**
     * Returns the library mode (e.g. 'live' or 'sandbox')
     *
     * @return string
     */
    public function getMode();

    /**
     * Checks if the callback verification is enabled
     *
     * @return bool
     */
    public function verifyCallbacks();

    /**
     * Return the credentials object
     *
     * @return Credentials
     */
    public function getCredentials();

}