<?php

namespace Hermes\Ink\Credentials;

use Hermes\Ink\Contracts\Credentials;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

class ClientCredentials implements Credentials
{


    /**
     * Client ID string
     *
     * @var string
     */
    protected $clientId;

    /**
     * Client secret
     *
     * @var string
     */
    protected $secret;

    /**
     * ClientCredentials constructor.
     *
     * @param $clientId
     * @param $secret
     */
    public function __construct($clientId, $secret)
    {

        $this->clientId = $clientId;
        $this->secret = $secret;

    }

    /**
     * Get the client id
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Get the client secret
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Return the credentials as array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            $this->getClientId(),
            $this->getSecret()
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