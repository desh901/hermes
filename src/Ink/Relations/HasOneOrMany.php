<?php

namespace Hermes\Ink\Relations;

use Hermes\Ink\Contracts\Object as HermesObject;

class HasOneOrMany extends Relation
{

    /**
     * The parent object attribute key where the relation is stored
     *
     * @var string
     */
    protected $parentKey;

    /**
     * HasOneOrMany constructor.
     *
     * @param string $related
     * @param HermesObject $parent
     * @param $parentKey
     */
    public function __construct($related, HermesObject $parent, $parentKey)
    {
        parent::__construct($related, $parent);
    }

    /**
     * Get the attribute key of the parent object
     *
     * @return string
     */
    public function getParentKey()
    {
        return $this->parentKey;
    }

    /**
     * Returns the relationship value
     *
     * @return mixed
     */
    public function getRelationValue()
    {
        return $this->getParent()->getAttribute($this->getParentKey());
    }

}