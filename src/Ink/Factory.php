<?php

namespace Hermes\Ink;

use Hermes\Core\Routing\ActionRouter;
use Hermes\Ink\Contracts\Factory as FactoryContract;
use Hermes\Core\Exceptions\EntityResolutionException;
use Illuminate\Support\Str;

class Factory implements FactoryContract
{

    /**
     * Which namespace to use in order to load actions
     * @var string
     */
    protected $actionsNamespace;

    /**
     * Action router
     *
     * @var ActionRouter
     */
    protected $router;

    /**
     * Api context
     *
     * @var \Hermes\Ink\Contracts\Context
     */
    protected $context;

    public function __construct(Context $context, ActionRouter $router)
    {

        $this->context = $context;
        $this->router = $router;

    }

    /**
     * Make a fake instance of a factory
     *
     * @param string $entity
     * @param int $statusCode
     * @param array $responseBody
     *
     * @return Action
     */
    public function fake($entity, $statusCode, array $responseBody)
    {

        $action = $this->router->getActions()->getByName($entity);

        $actionClass = get_class($action);
        return $actionClass::fake($this->context, $statusCode, json_encode($responseBody));

    }

    /**
     * Get an action instance
     *
     * @param string $entity
     *
     * @return Action
     * @throws EntityResolutionException
     */
    public function make($entity)
    {

        if(!$this->router->getActions()->hasNamedAction($entity)) {
            throw new EntityResolutionException($entity);
        }

        return $this->router->getActions()->getByName($entity);

    }

    /**
     * Dynamically instantiate entities from factory
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {

        $snakedMethod = Str::snake($method, '.');
        if($this->router->getActions()->hasNamedAction($snakedMethod))
        {
            return $this->make($snakedMethod);
        }

        throw new \BadMethodCallException("Method [$method] does not exist in class " . self::class);

    }

}