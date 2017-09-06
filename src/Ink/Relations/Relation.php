<?php

namespace Hermes\Ink\Relations;


use Hermes\Ink\Contracts\Object as HermesObject;
use Illuminate\Support\Traits\Macroable;

abstract class Relation
{

    use Macroable {
        __call as macroCall;
    }

    /**
     * The parent object instance.
     *
     * @var HermesObject
     */
    protected $parent;

    /**
     * The related object class.
     *
     * @var string
     */
    protected $related;

    /**
     * Create a new relation instance.
     *
     * @param $related
     * @param HermesObject $parent
     */
    public function __construct($related, HermesObject $parent)
    {

        $this->related = $related;
        $this->parent = $parent;

    }

    /**
     * Get the parent object of the relation
     *
     * @return HermesObject
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get the related object of the relation.
     *
     * @return string
     */
    public function getRelated()
    {
        return $this->related;
    }

    /**
     * Returns the value of the relationship
     *
     * @return mixed
     */
    public abstract function getRelationValue();

    /**
     * Handle dynamic method calls to the relationship.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {

        if(static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return null;
    }

}