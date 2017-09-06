<?php

namespace Hermes\Core\Contracts\Parsing;


interface Parser
{


    /**
     * Parses the request body
     *
     * @param $body
     * @return mixed
     */
    public function parse($body);


}