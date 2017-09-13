<?php

namespace Hermes\Core\Contracts\Routing;


use Hermes\Ink\Action;

interface Router
{

    /**
     * Register a new GET action with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Action
     */
    public function get($uri, $action = null);

    /**
     * Register a new POST action with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Action
     */
    public function post($uri, $action = null);

    /**
     * Register a new PUT action with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Action
     */
    public function put($uri, $action = null);

    /**
     * Register a new PATCH action with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Action
     */
    public function patch($uri, $action = null);

    /**
     * Register a new DELETE action with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Action
     */
    public function delete($uri, $action = null);

    /**
     * Register a new OPTIONS action with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Action
     */
    public function options($uri, $action = null);

    /**
     * Create an action group with shared attributes.
     *
     * @param  array  $attributes
     * @param  \Closure|string  $actions
     * @return void
     */
    public function group(array $attributes, $actions);

}