<?php

namespace Hermes\Core\Parsing;

use Illuminate\Support\Arr;
use Illuminate\Support\Manager;
use Hermes\Core\Parsing\Parsers\RawParser;
use Hermes\Core\Parsing\Parsers\JsonParser;
use Hermes\Core\Contracts\Parsing\Factory as FactoryContract;

class ParsingManager extends Manager implements FactoryContract
{

    /**
     * MimeType mapping
     *
     * @var array
     */
    protected $mimeTypesMapping = [
        'application/json' => 'json',
        'application/x-javascript' => 'json',
        'text/x-json' => 'json',
        'text/javascript' => 'json',
        'text/x-javascript' => 'json',
        'text/html' => 'raw'
    ];

    /**
     * The default request body parser
     *
     * @var string
     */
    protected $defaultParser = 'json';

    /**
     * Get the default parser name
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->defaultParser;
    }

    /**
     * Maps MimeTypes into parsers types
     *
     * @param string $mimeType
     * @return string
     */
    protected function mapMimeType($mimeType = null)
    {

        if(is_null($mimeType)) return null;

        Arr::get($this->mimeTypesMapping, strtolower($mimeType));
    }

    /**
     * Get a parser instance
     *
     * @param string|null $mimeType
     * @return mixed
     */
    public function parserFor($mimeType = null)
    {
        $type = $this->mapMimeType($mimeType);
        return $this->driver($type);
    }

    /**
     * Create an instance of the JSON driver
     *
     * @return JsonParser
     */
    protected function createJsonDriver()
    {
        return $this->app->make(JsonParser::class);
    }

    /**
     * Create an instance of Raw driver
     *
     * @return RawParser
     */
    protected function createRawDriver()
    {
        return $this->app->make(RawParser::class);
    }

}