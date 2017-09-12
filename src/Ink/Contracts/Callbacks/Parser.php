<?php

namespace Hermes\Ink\Contracts\Callbacks;


use Symfony\Component\HttpFoundation\Request;
use Hermes\Core\Contracts\Parsing\Factory as RequestParser;

interface Parser
{


    /**
     * Parser constructor.
     *
     * @param Request $request
     * @param RequestParser $parser
     * @param Verifier $verifier
     */
    public function __construct(Request $request, Verifier $verifier, RequestParser $parser);

    /**
     * Parse the raw payload to the specific callback object
     *
     * @return \Hermes\Ink\Callbacks\Callback
     */
    public function parse();

}