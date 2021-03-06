<?php

namespace Hermes\Ink;

use ArrayAccess;
use JsonSerializable;
use Illuminate\Support\Arr;
use Hermes\Ink\Relations\HasOne;
use Hermes\Ink\Relations\Relation;
use Illuminate\Support\Collection;
use Hermes\Ink\Traits\HasAttributes;
use Hermes\Ink\Traits\HasArrayAccess;
use Hermes\Ink\Contracts\Relationable;
use Hermes\Ink\Traits\HidesAttributes;
use Hermes\Ink\Traits\HasRelationships;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Hermes\Core\Exceptions\ObjectParsingException;
use Hermes\Ink\Contracts\Object as ObjectContract;

abstract class Object implements ObjectContract, Arrayable, ArrayAccess, Jsonable, JsonSerializable, Relationable
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
    protected $keyName;


    public function __construct($keyName)
    {

        $this->keyName = $keyName ?: $this->keyName;

    }

    /**
     * Create an object or a collection of objects from an attributes array
     *
     * @param array $attributes
     * @param string $keyName
     * @return \Hermes\Ink\Object|Collection
     * @throws ObjectParsingException
     */
    public static function create(array $attributes = [], $keyName = null)
    {

        $object = new static($keyName);

        if($object->isSingleObject($attributes)) {

            $object->parse(
                $object->getObject($attributes)
            );

            return $object;

        }else if($object->isMultipleObjects($attributes))
        {

            $collection = $object->getObject($attributes);
            $objects = array_map(function($attributes) use($object){
                return self::create($attributes, $object->keyName);
            }, $collection);

            return collect($objects);

        }else {
            throw new ObjectParsingException($object, $attributes);
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
            $this->getObject($attributes)
        );
    }

    /**
     * Returns the attributes that reflects the object into the array
     *
     * @param array $attributes
     * @return array|mixed
     */
    protected function getObject(array $attributes)
    {
        return $this->keyName
            ? Arr::get($attributes, $this->keyName, $attributes)
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
            if($this->isRelation($attribute)) {
                $relationName = $this->getRelationNameFromAttribute($attribute);
                $relation = $this->{$relationName}();
                $this->setRelation(
                    $relationName,
                    $this->parseRelation($relation, $value)
                );

            }else {
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
            return $relationClass::create($value, '');

        }else {

            $objects = array_map(function($obj) use($relationClass){
                return $relationClass::create($obj, '');
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