<?php

namespace Hermes\Core\Routing;

use Closure;
use Hermes\Ink\Action;
use Illuminate\Support\Str;
use Illuminate\Container\Container;
use Illuminate\Support\Traits\Macroable;

class ActionRouter
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * The action collection instance.
     *
     * @var ActionCollection
     */
    protected $actions;

    /**
     * The currently dispatched action instance.
     *
     * @var Action
     */
    protected $current;

    /**
     * The action group attribute stack.
     *
     * @var array
     */
    protected $groupStack = [];

    /**
     * All of the verbs supported by the router.
     *
     * @var array
     */
    public static $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    /**
     * Create a new Router instance.
     *
     * @param  \Illuminate\Container\Container  $container
     */
    public function __construct(Container $container = null)
    {
        $this->actions = new ActionCollection;
        $this->container = $container ?: new Container;
    }

    /**
     * Register a new GET action with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Action
     */
    public function get($uri, $action = null)
    {
        return $this->addAction('GET', $uri, $action);
    }

    /**
     * Register a new POST action with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Action
     */
    public function post($uri, $action = null)
    {
        return $this->addAction('POST', $uri, $action);
    }

    /**
     * Register a new PUT action with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Action
     */
    public function put($uri, $action = null)
    {
        return $this->addAction('PUT', $uri, $action);
    }

    /**
     * Register a new PATCH action with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Action
     */
    public function patch($uri, $action = null)
    {
        return $this->addAction('PATCH', $uri, $action);
    }

    /**
     * Register a new DELETE action with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Action
     */
    public function delete($uri, $action = null)
    {
        return $this->addAction('DELETE', $uri, $action);
    }

    /**
     * Register a new OPTIONS action with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Action
     */
    public function options($uri, $action = null)
    {
        return $this->addAction('OPTIONS', $uri, $action);
    }

    /**
     * Create an action group with shared attributes.
     *
     * @param  array  $attributes
     * @param  \Closure|string  $actions
     * @return void
     */
    public function group(array $attributes, $actions)
    {
        dump('BEFORE');
        dump($attributes);
        dump($actions);
        $this->updateGroupStack($attributes);

        dump('AFTER');
        dump($this->groupStack);
        dump($actions);

        // Once we have updated the group stack, we'll load the provided actions and
        // merge in the group's attributes when the actions are created. After we
        // have created the actions, we will pop the attributes off the stack.
        $this->loadActions($actions);

        array_pop($this->groupStack);
    }

    /**
     * Update the group stack with the given attributes.
     *
     * @param  array  $attributes
     * @return void
     */
    protected function updateGroupStack(array $attributes)
    {
        if (! empty($this->groupStack)) {
            $attributes = ActionGroup::merge($attributes, end($this->groupStack));
        }

        $this->groupStack[] = $attributes;
    }

    /**
     * Merge the given array with the last group stack.
     *
     * @param  array  $new
     * @return array
     */
    public function mergeWithLastGroup($new)
    {
        return ActionGroup::merge($new, end($this->groupStack));
    }

    /**
     * Load the provided actions.
     *
     * @param  \Closure|string  $actions
     * @return void
     */
    protected function loadActions($actions)
    {
        if ($actions instanceof Closure) {
            $actions($this);
        } else {
            $router = $this;

            require $actions;
        }
    }

    /**
     * Get the prefix from the last group on the stack.
     *
     * @return string
     */
    public function getLastGroupPrefix()
    {
        if (! empty($this->groupStack)) {
            $last = end($this->groupStack);

            return isset($last['prefix']) ? $last['prefix'] : '';
        }

        return '';
    }

    /**
     * Add a action to the underlying action collection.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  \Closure|array|string|null  $class
     * @return Action
     */
    protected function addAction($method, $uri, $class)
    {
        return $this->actions->add($this->createAction($method, $uri, $class));
    }

    /**
     * Create a new action instance.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  mixed  $class
     * @return Action
     */
    protected function createAction($method, $uri, $class)
    {

        $action = $this->newAction(
            $method, $this->prefix($uri), $this->prependGroupNamespace($class)
        );

        // If we have groups that need to be merged, we will merge them now after this
        // action has already been created and is ready to go. After we're done with
        // the merge we will be ready to return the action back out to the caller.
        if ($this->hasGroupStack()) {
            $this->mergeGroupAttributesIntoAction($action);
        }

        return $action;
    }

    /**
     * Prepend the last group namespace onto the use clause.
     *
     * @param  string  $class
     * @return string
     */
    protected function prependGroupNamespace($class)
    {
        $group = end($this->groupStack);

        return isset($group['namespace']) && strpos($class, '\\') !== 0
            ? $group['namespace'].'\\'.$class : $class;
    }

    /**
     * Create a new Action object.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  mixed  $class
     * @return Action
     */
    protected function newAction($method, $uri, $class)
    {

        $action = $this->container->make($class);

        return $action->setMethod($method)
            ->setEndpoint($uri);
    }

    /**
     * Prefix the given URI with the last prefix.
     *
     * @param  string  $uri
     * @return string
     */
    protected function prefix($uri)
    {
        return trim(trim($this->getLastGroupPrefix(), '/').'/'.trim($uri, '/'), '/') ?: '/';
    }

    /**
     * Merge the group stack with the action classname.
     *
     * @param  Action  $action
     * @return void
     */
    protected function mergeGroupAttributesIntoAction($action)
    {
        $action->setOptions($this->mergeWithLastGroup($action->getOptions()));
    }

    /**
     * Determine if the router currently has a group stack.
     *
     * @return bool
     */
    public function hasGroupStack()
    {
        return ! empty($this->groupStack);
    }

    /**
     * Get the current group stack for the router.
     *
     * @return array
     */
    public function getGroupStack()
    {
        return $this->groupStack;
    }

    /**
     * Get the currently dispatched action instance.
     *
     * @return Action
     */
    public function getCurrentAction()
    {
        return $this->current();
    }

    /**
     * Get the currently dispatched action instance.
     *
     * @return Action
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * Check if a action with the given name exists.
     *
     * @param  string  $name
     * @return bool
     */
    public function has($name)
    {
        return $this->actions->hasNamedAction($name);
    }

    /**
     * Get the current action name.
     *
     * @return string|null
     */
    public function currentActionName()
    {
        return $this->current() ? $this->current()->getName() : null;
    }

    /**
     * Alias for the "currentActionNamed" method.
     *
     * @return bool
     */
    public function is()
    {
        foreach (func_get_args() as $pattern) {
            if (Str::is($pattern, $this->currentActionName())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the current action matches a given name.
     *
     * @param  string  $name
     * @return bool
     */
    public function currentActionNamed($name)
    {
        return $this->current() ? $this->current()->getName() === $name : false;
    }

    /**
     * Get the current action class.
     *
     * @return string|null
     */
    public function currentActionClass()
    {
        if (! $this->current()) {
            return null;
        }

        return get_class($this->current());
    }

    /**
     * Get the underlying actions collection.
     *
     * @return ActionCollection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Set the action collection instance.
     *
     * @param  ActionCollection  $actions
     * @return void
     */
    public function setActions($actions)
    {

        $this->actions = $actions;

        $this->container->instance('actions', $this->actions);
    }

    /**
     * Dynamically handle calls into the router instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return (new ActionRegistrar($this))->attribute($method, $parameters[0]);
    }
}
