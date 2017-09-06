<?php

namespace Hermes\Core\Contracts\Parsing;


interface Factory
{


    /**
     * Get a parser instance
     *
     * @param string|null $mimeType
     * @return Parser
     */
    public function parserFor($mimeType = null);

}