<?php
/**
 * Created by PhpStorm.
 * User: desh
 * Date: 06/09/17
 * Time: 16.02
 */

namespace Hermes\Ink\Traits;


trait HasArrayAccess
{

    /**
     * Determine if the given attribute exists
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * Get the value for a given offset
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Set the value for a given offset.
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Unset the value for a given offset
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }

    /**
     * Determine if an attribute or relation exists on the object
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->$key);
    }

    /**
     * Unset an attribute on the object
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->$key);
    }

}