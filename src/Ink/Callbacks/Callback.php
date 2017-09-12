<?php

namespace Hermes\Ink\Callbacks;

use Illuminate\Support\Arr;
use Hermes\Ink\Relations\HasOne;
use Illuminate\Support\Collection;
use Hermes\Ink\Relations\Relation;
use Hermes\Ink\Traits\HasAttributes;
use Hermes\Ink\Traits\HasArrayAccess;
use Hermes\Ink\Contracts\Relationable;
use Hermes\Ink\Traits\HidesAttributes;
use Hermes\Ink\Traits\HasRelationships;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Hermes\Ink\Contracts\Callbacks\Callback as CallbackContract;

abstract class Callback implements CallbackContract, Arrayable, \ArrayAccess, Jsonable, \JsonSerializable, Relationable
{

    use HasAttributes,
        HasArrayAccess,
        HidesAttributes,
        HasRelationships;

    /**
     * Callback object structure
     *
     * @var array
     */
    protected $structure = [];


    /**
     * Parses the callback instance from the payload
     *
     * @param array $payload
     *
     * @return \Hermes\Ink\Callback
     */
    public static function parse(array $payload)
    {

        $callback = new static();

        foreach ($callback->structure as $attribute)
        {

            $value = Arr::get($payload, $attribute);

            if(is_null($value)) continue;

            /*
             * If the object has a method called like the attribute studly
             * it means that we have to load the relationship else we just set
             * the attribute value
             */
            if($callback->isRelation($attribute)) {
                $relationName = $callback->getRelationNameFromAttribute($attribute);
                $relation = $callback->{$relationName}();
                $callback->setRelation(
                    $relationName,
                    $callback->parseRelation($relation, $value)
                );

            }else {
                $callback->setAttribute($attribute, $value);
            }

        }

        return $callback;
    }

    /**
     * Parse a nested relationship
     *
     * @param Relation $relation
     * @param array $value
     *
     * @return Object|Collection
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
     * Serialize the Callback object to array
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            $this->attributesToArray(),
            $this->relationsToArray()
        );
    }

    /**
     * Encode the callback to a JSON object
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert callback into something JSON Serializable
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert object to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }
}