<?php

namespace Hermes\Ink;

use ArrayAccess;
use JsonSerializable;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Hermes\Ink\Relations\HasOne;
use Hermes\Ink\Relations\Relation;
use Illuminate\Support\Collection;
use Hermes\Ink\Traits\HasAttributes;
use Hermes\Ink\Traits\HasArrayAccess;
use Hermes\Ink\Traits\HidesAttributes;
use Hermes\Ink\Traits\HasRelationships;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Hermes\Ink\Contracts\Object as ObjectContract;

abstract class Object implements ObjectContract, Arrayable, ArrayAccess, Jsonable, JsonSerializable
{

    use HasAttributes,
        HasArrayAccess,
        HidesAttributes,
        HasRelationships;

    /**
     * Base item key where to retrieve the object
     *
     * @var string
     */
    protected $itemKeyName;

    /**
     * Base collection name where to retrieve the object collection
     *
     * @param array $attributes
     */
    protected $collectionKeyName;


    public function __construct($itemKeyName = null, $collectionKeyName = null)
    {

        $this->itemKeyName = $itemKeyName ?: $this->itemKeyName;
        $this->collectionKeyName = $collectionKeyName ?: $this->collectionKeyName;

    }

    public static function create(array $attributes = [], $itemKeyName = null, $collectionKeyName)
    {

        $object = new static($itemKeyName, $collectionKeyName);

        if($object->isSingleObject($attributes)) {

            $object->parse(
                $object->getObject($attributes)
            );

            return $object;

        }else if($object->isMultipleObjects($attributes))
        {

            $collection = $object->getObjectCollection($attributes);
            $objects = array_map(function($attributes) use($object){
                return self::create($attributes, $object->itemKeyName, $object->collectionKeyName);
            }, $collection);

            return collect($objects);

        }else {
            throw new \Exception('Cannot parse object');
        }
    }

    /**
     * Checks if provided attributes array contains a single object reference
     *
     * @param array $attributes
     * @return bool
     */
    protected function isSingleObject(array $attributes)
    {
        return Arr::isAssoc(
            $this->getObject($attributes)
        );
    }

    /**
     * Checks if the provided attributes array contains a collection of the object
     *
     * @param array $attributes
     * @return bool
     */
    protected function isMultipleObjects(array $attributes)
    {
        return !Arr::isAssoc(
            $this->getObjectCollection($attributes)
        );
    }

    /**
     * Returns the collection of objects in the attributes array
     *
     * @param array $attributes
     * @return array|mixed
     */
    protected function getObjectCollection(array $attributes)
    {
        return $this->collectionKeyName
            ? Arr::get($attributes, $this->collectionKeyName, $attributes)
            : $attributes;
    }

    /**
     * Returns the attributes that reflects the object into the array
     *
     * @param array $attributes
     * @return array|mixed
     */
    protected function getObject(array $attributes)
    {
        return $this->itemKeyName
            ? Arr::get($attributes, $this->itemKeyName, $attributes)
            : $attributes;
    }

    /**
     * Parses the raw data to this object instance or a collection of objects
     *
     * @param array $attributes
     */
    protected function parse(array $attributes)
    {

        foreach ($this->structure as $attribute)
        {

            $value = Arr::get($attributes, $attribute);

            if(is_null($value)) continue;

            /*
             * If the object has a method called like the attribute studly
             * it means that we have to load the relationship else we just set
             * the attribute value
             */
            $relationMethod = Str::studly($attribute);
            if(method_exists($this, $relationMethod)) {

                $relation = $this->$relationMethod();
                if($relation instanceof Relation) {
                    $this->setRelation(
                        $attribute,
                        $this->parseRelation($relation, $value)
                    );
                }

            }else{
                $this->setAttribute($attribute, $value);
            }

        }

    }

    /**
     * Parse a nested relationship
     *
     * @param Relation $relation
     * @param array $value
     *
     * @return ObjectContract|Collection
     */
    protected function parseRelation(Relation $relation, array $value)
    {

        $relationClass = $relation->getRelated();

        if($relation instanceof HasOne)
        {
            return $relationClass::create($value, '', '');

        }else {

            $objects = array_map(function($obj) use($relationClass){
                return $relationClass::create($obj, '', '');
            }, $value);

            return collect($objects);
        }

    }

    /**
     * Convert object to array
     *
     * @param bool $stripEmpty
     * @return array
     */
    public function toArray($stripEmpty = true)
    {

        $arr = array_merge(
            $this->attributesToArray(),
            $this->relationsToArray()
        );

        if($stripEmpty) {
            $arr = array_filter($arr, function ($value) {
                return !is_null($value);
            });
        }

        return $arr;
    }

    /**
     * Convert object to json
     *
     * @param int $options
     * @param bool $stripEmpty
     * @return string
     */
    public function toJson($options = 0, $stripEmpty = true)
    {

        return json_encode($this->jsonSerialize($stripEmpty), $options);

    }

    /**
     * Convert object into something JSON serializable.
     *
     * @param bool $stripEmpty
     * @return array
     */
    public function jsonSerialize($stripEmpty = false)
    {
        return $this->toArray($stripEmpty);
    }

    /**
     * Transform object to a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson(false);
    }

}