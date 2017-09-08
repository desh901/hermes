<?php

namespace Hermes\Ink\Credentials;

use Hermes\Ink\Contracts\Credentials\Credentials;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

class BasicCredentials implements Credentials
{


    /**
     * Username
     *
     * @var string
     */
    protected $username;

    /**
     * Password
     *
     * @var string
     */
    protected $password;

    /**
     * BasicCredentials constructor.
     *
     * @param $username
     * @param $password
     */
    public function __construct($username, $password)
    {

        $this->username = $username;
        $this->password = $password;

    }

    /**
     * Get the username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get the password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Return the credentials as array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            $this->getUsername(),
            $this->getPassword()
        ];
    }

    /**
     * Convert the credentials to an HTTP Basic authentication string
     *
     * @return string
     */
    protected function toHttpBasic()
    {

        return base64_encode(join(':', $this->toArray()));

    }

    /**
     * Apply credentials to the request
     *
     * @param GuzzleRequest $request
     * @return mixed
     */
    public function apply(GuzzleRequest $request)
    {

        return $request->withHeader('Authorization', 'Basic ' . $this->toHttpBasic());

    }


}