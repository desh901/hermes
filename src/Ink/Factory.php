<?php

namespace Hermes\Ink;

use Hermes\Core\Exceptions\EntityResolutionException;
use Hermes\Ink\Contracts\Action;
use Hermes\Ink\Contracts\Context;
use Hermes\Ink\Contracts\Factory as FactoryContract;
use Illuminate\Support\Arr;

class Factory implements FactoryContract
{

    /**
     * Which namespace to use in order to load actions
     * @var string
     */
    protected $actionsNamespace;

    /**
     * Available actions
     *
     * @var array
     */
    protected $actions = [];

    /**
     * Api context
     *
     * @var Context
     */
    protected $context;

    public function __construct(Context $context)
    {

        $this->context = $context;

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

        $actionClass = $this->getActionClass($entity);
        return $actionClass::fake($this->context, $statusCode, json_encode($responseBody));

    }

    /**
     * Get an action instance
     *
     * @param string $entity
     *
     * @return Action
     */
    public function make($entity)
    {

        return hermes($this->getActionClass($entity));

    }

    /**
     * Return the class name of the requested action
     *
     * @param $entity
     * @return string
     */
    protected function getActionClass($entity)
    {
        $this->entityExists($entity);

        return $this->actionsNamespace . '\\' . Arr::get($this->actions, $entity);
    }

    /**
     * Checks whether the requested action is instantiable
     *
     * @param string $entity
     *
     * @throws EntityResolutionException
     */
    protected function entityExists($entity)
    {

        if(!Arr::has($this->actions, $entity))
            throw new EntityResolutionException($entity);

    }

}