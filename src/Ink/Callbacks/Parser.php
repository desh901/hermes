<?php

namespace Hermes\Ink\Callbacks;


use Hermes\Ink\Contracts\Callbacks\Verifier;
use Symfony\Component\HttpFoundation\Request;
use Hermes\Core\Exceptions\UnexpectedCallbackException;
use Hermes\Core\Contracts\Parsing\Factory as RequestParser;
use Hermes\Ink\Contracts\Callbacks\Parser as ParserContract;

class Parser implements ParserContract
{

    /**
     * All the application callbacks classes
     *
     * @var array
     */
    protected $callbacks = [];

    /**
     * The callback verifier instance
     *
     * @var Verifier
     */
    protected $verifier;

    /**
     * The request instance
     *
     * @var Request
     */
    protected $request;

    /**
     * The request parser instance
     *
     * @var RequestParser
     */
    protected $parser;

    /**
     * CallbackParser constructor.
     *
     * @param Request $request
     * @param Verifier $verifier
     * @param RequestParser $parser
     */
    public function __construct(Request $request, Verifier $verifier, RequestParser $parser)
    {

        $this->verifier = $verifier;
        $this->request = $request;
        $this->parser = $parser;

    }

    /**
     * Parse the raw payload to the specific callback object
     *
     * @return \Hermes\Ink\Callbacks\Callback
     * @throws UnexpectedCallbackException
     */
    public function parse()
    {

        $this->verifier->verify($this->request);
        $requestParser = $this->parser->parserFor($this->request->getContentType());
        $payload = $requestParser->parse($this->request->getContent());

        foreach ($this->callbacks as $callback)
        {

            if($callback::isInstance($payload))
            {
                return $callback::parse($payload);
            }

        }

        throw new UnexpectedCallbackException($payload, $this->request);
    }

}