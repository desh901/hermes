<?php

namespace Hermes\Core\Routing;

use Countable;
use ArrayIterator;
use Hermes\Ink\Action;
use IteratorAggregate;
use Illuminate\Support\Arr;

class ActionCollection implements Countable, IteratorAggregate
{
    /**
     * An array of the actions keyed by method.
     *
     * @var array
     */
    protected $actions = [];

    /**
     * An flattened array of all of the actions.
     *
     * @var array
     */
    protected $allActions = [];

    /**
     * A look-up table of actions by their names.
     *
     * @var array
     */
    public $nameList = [];

    /**
     * A look-up table of actions by class.
     *
     * @var array
     */
    protected $actionList = [];

    /**
     * Add a Route instance to the collection.
     *
     * @param  Action  $action
     * @return Action
     */
    public function add(Action $action)
    {
        $this->addToCollections($action);

        $this->addLookups($action);

        return $action;
    }

    /**
     * Add the given action to the arrays of routes.
     *
     * @param  Action  $action
     * @return void
     */
    protected function addToCollections($action)
    {
        $domainAndUri = $action->getBaseUrl().$action->getEndpoint();

        $method = $action->getMethod();
        $this->actions[$method][$domainAndUri] = $action;

        $this->allActions[$method.$domainAndUri] = $action;
    }

    /**
     * Add the action to any look-up tables if necessary.
     *
     * @param  Action  $action
     * @return void
     */
    protected function addLookups($action)
    {
        $this->nameList[$action->getName()] = $action;

        $this->addToActionList($action);
    }

    /**
     * Add an action to the class action dictionary.
     *
     * @param  Action  $action
     * @return void
     */
    protected function addToActionList($action)
    {
        $this->actionList[trim(get_class($action), '\\')] = $action;
    }

    /**
     * Refresh the name look-up table.
     *
     * This is done in case any names are fluently defined or if routes are overwritten.
     *
     * @return void
     */
    public function refreshNameLookups()
    {
        $this->nameList = [];

        foreach ($this->allActions as $action) {
            if ($action->getName()) {
                $this->nameList[$action->getName()] = $action;
            }
        }
    }

    /**
     * Refresh the class look-up table.
     *
     * This is done in case any actions are overwritten with new controllers.
     *
     * @return void
     */
    public function refreshActionLookups()
    {
        $this->actionList = [];

        foreach ($this->allActions as $action) {
            $this->addToActionList($action);
        }
    }

    /**
     * Get routes from the collection by method.
     *
     * @param  string|null  $method
     * @return array
     */
    public function get($method = null)
    {
        return is_null($method) ? $this->getActions() : Arr::get($this->actions, $method, []);
    }

    /**
     * Determine if the action collection contains a given named route.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasNamedAction($name)
    {
        return ! is_null($this->getByName($name));
    }

    /**
     * Get a action instance by its name.
     *
     * @param  string  $name
     * @return Action|null
     */
    public function getByName($name)
    {
        return isset($this->nameList[$name]) ? $this->nameList[$name] : null;
    }

    /**
     * Get a action instance by its controller action.
     *
     * @param  string $actionClass
     * @return Action|null
     */
    public function getByClass($actionClass)
    {
        return isset($this->actionList[$actionClass]) ? $this->actionList[$actionClass] : null;
    }

    /**
     * Get all of the actions in the collection.
     *
     * @return array
     */
    public function getActions()
    {
        return array_values($this->allActions);
    }

    /**
     * Get all of the actions keyed by their HTTP verb / method.
     *
     * @return array
     */
    public function getActionsByMethod()
    {
        return $this->actions;
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getActions());
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count()
    {
        return count($this->getActions());
    }
}
