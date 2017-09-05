<?php

namespace Hermes\Core\Contracts;


interface Application
{

    /**
     * Call the given Closure / class@method and inject its dependencies.
     *
     * @param  callable|string  $callback
     * @param  array  $parameters
     * @param  string|null  $defaultMethod
     * @return mixed
     */
    public function call($callback, array $parameters = [], $defaultMethod = null);

    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version();
}