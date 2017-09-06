<?php

namespace Hermes\Core\Parsing\Parsers;

use Hermes\Core\Contracts\Parsing\Parser;

class RawParser implements Parser
{

    /**
     * Parses the request body
     *
     * @param $body
     * @return string
     */
    public function parse($body)
    {

        return $body;

    }


}