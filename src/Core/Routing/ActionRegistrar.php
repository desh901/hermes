<?php

namespace Hermes\Core\Routing;

use Closure;
use BadMethodCallException;
use Hermes\Ink\Action;
use InvalidArgumentException;

class ActionRegistrar
{
    /**
     * The router instance.
     *
     * @var \Hermes\Core\Routing\ActionRouter
     */
    protected $router;

    /**
     * The attributes to pass on to the router.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The methods to dynamically pass through to the router.
     *
     * @var array
     */
    protected $passthru = [
        'get', 'post', 'put', 'patch', 'delete', 'options', 'any',
    ];

    /**
     * The attributes that can be set through this class.
     *
     * @var array
     */
    protected $allowedAttributes = [
        'as', 'name', 'namespace', 'prefix', 'timeout'
    ];

    /**
     * The attributes that are aliased.
     *
     * @var array
     */
    protected $aliases = [
        'name' => 'as',
    ];

    /**
     * Create a new route registrar instance.
     *
     * @param  \Hermes\Core\Routing\ActionRouter  $router
     */
    public function __construct(ActionRouter $router)
    {
        $this->router = $router;
    }

    /**
     * Set the value for a given attribute.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function attribute($key, $value)
    {
        if (! in_array($key, $this->allowedAttributes)) {
            throw new InvalidArgumentException("Attribute [{$key}] does not exist.");
        }

        $this->attributes[array_get($this->aliases, $key, $key)] = $value;

        return $this;
    }

    /**
     * Create a route group with shared attributes.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public function group($callback)
    {
        $this->router->group($this->attributes, $callback);
    }

    /**
     * Register a new route with the router.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  string  $class
     * @return Action
     */
    protected function registerAction($method, $uri, $class = null)
    {
        return $this->router->{$method}($uri, $class);
    }

    /**
     * Dynamically handle calls into the route registrar.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return Action|$this
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, $this->passthru)) {
            return $this->registerAction($method, ...$parameters);
        }

        if (in_array($method, $this->allowedAttributes)) {
            return $this->attribute($method, $parameters[0]);
        }

        throw new BadMethodCallException("Method [{$method}] does not exist.");
    }
}
