<?php

namespace Hermes\Core\Parsing\Parsers;

use Hermes\Core\Contracts\Parsing\Parser;

class JsonParser implements Parser
{

    /**
     * Parses the request body
     *
     * @param $body
     * @return array
     */
    public function parse($body)
    {

        return json_decode($body, true);

    }


}