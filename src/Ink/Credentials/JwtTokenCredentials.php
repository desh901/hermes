<?php

namespace Hermes\Ink\Credentials;

use Hermes\Ink\Contracts\Credentials;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

class JwtTokenCredentials implements Credentials
{


    /**
     * The authentication token string
     *
     * @var string
     */
    protected $token;

    /**
     * JwtTokenCredentials constructor.
     *
     * @param $token
     */
    public function __construct($token)
    {

        $this->token = $token;

    }

    /**
     * Get the authentication token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Apply credentials to the request
     *
     * @param GuzzleRequest $request
     * @return mixed
     */
    public function apply(GuzzleRequest $request)
    {

        return $request->withHeader('Authorization', 'Bearer ' . $this->getToken());

    }


}