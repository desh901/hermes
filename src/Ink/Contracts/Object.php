<?php

namespace Hermes\Ink\Contracts;

use Illuminate\Support\Collection;

interface Object
{


    /**
     * Create an object or a collection of objects from an attributes array
     *
     * @param array $attributes
     * @param string $itemKeyName
     * @param string $collectionKeyName
     * @return Object|Collection
     */
    public static function create(array $attributes, $itemKeyName, $collectionKeyName);

    /**
     * Convert object to array
     *
     * @param bool $stripEmpty
     * @return array
     */
    public function toArray($stripEmpty = true);

    /**
     * Determine if the given attribute exists
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset);

    /**
     * Get the value for a given offset
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset);

    /**
     * Set the value for a given offset.
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value);

    /**
     * Unset the value for a given offset
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset);

    /**
     * Determine if an attribute or relation exists on the object
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key);

    /**
     * Unset an attribute on the object
     *
     * @param string $key
     * @return void
     */
    public function __unset($key);

    /**
     * Transform object to a string
     *
     * @return string
     */
    public function __toString();

}