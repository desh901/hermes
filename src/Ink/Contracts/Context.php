<?php

namespace Hermes\Ink\Contracts;

interface Context
{

    /**
     * Returns the request base url
     *
     * @return string
     */
    public function getBaseUrl();

    /**
     * Returns the request timeout
     *
     * @return int
     */
    public function getTimeout();

    /**
     * Returns the api environment
     *
     * @return string
     */
    public function getMode();

    /**
     * Whether the callbacks verification module should be enabled
     *
     * @return bool
     */
    public function verifyCallbacks();

    /**
     * Returns the credentials instance
     *
     * @return Credentials
     */
    public function getCredentials();
}