<?php

namespace Hermes\Ink\Credentials;


use Illuminate\Support\Manager;

class CredentialsManager extends Manager
{

    /**
     * Get the config value
     *
     * @param string $name
     * @return mixed
     */
    protected function getConfig($name)
    {

        return $this->app['config']['hermes']['credentials'][$name];

    }

    /**
     * Get the configuration for the specified mode
     *
     * @param string $name
     * @return mixed
     */
    protected function getConfigForMode($name)
    {

        $mode = $this->app['config']['hermes.mode'];

        return $this->app['config']['hermes'][$mode]['credentials'][$name];

    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {

        return $this->app['config']['hermes.credentials.default'];

    }

    /**
     * Creates the basic credentials driver
     *
     * @return BasicCredentials
     */
    protected function createBasicDriver()
    {
        $username = $this->getConfigForMode('username');
        $password = $this->getConfigForMode('password');
        return new BasicCredentials($username, $password);
    }

    /**
     * Creates the client credentials driver
     *
     * @return ClientCredentials
     */
    protected function createClientDriver()
    {
        $client_id = $this->getConfigForMode('client_id');
        $secret = $this->getConfigForMode('secret');
        return new ClientCredentials($client_id, $secret);
    }

    /**
     * Creates the JWT credentials driver
     *
     * @return JwtTokenCredentials
     */
    protected function createJwtDriver()
    {
        $token = $this->getConfigForMode('token');
        return new JwtTokenCredentials($token);
    }

}