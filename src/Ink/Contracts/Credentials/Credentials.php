<?php

namespace Hermes\Ink\Contracts\Credentials;

use GuzzleHttp\Psr7\Request as GuzzleRequest;

interface Credentials
{

    /**
     * Apply credentials to the request
     *
     * @param GuzzleRequest $request
     * @return mixed
     */
    public function apply(GuzzleRequest $request);

}