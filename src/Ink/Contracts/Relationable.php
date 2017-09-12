<?php

namespace Hermes\Ink\Contracts;


use Hermes\Ink\Relations\HasOne;
use Hermes\Ink\Relations\HasMany;

interface Relationable
{

    /**
     * Define a one-to-one relationship.
     *
     * @param  string  $related
     * @param  string  $parentKey
     * @return HasOne
     */
    public function hasOne($related, $parentKey = null);

    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $parentKey
     * @return HasMany
     */
    public function hasMany($related, $parentKey = null);

    /**
     * Get the default parent key name for the object.
     *
     * @param string $related
     * @return string
     */
    public function getParentKey($related);

    /**
     * Get all the loaded relations for the instance.
     *
     * @return array
     */
    public function getRelations();

    /**
     * Get a specified relationship.
     *
     * @param  string  $relation
     * @return mixed
     */
    public function getRelation($relation);
    /**
     * Determine if the given relation is loaded.
     *
     * @param  string  $key
     * @return bool
     */
    public function relationLoaded($key);

    /**
     * Set the specific relationship in the model.
     *
     * @param  string  $relation
     * @param  mixed  $value
     * @return $this
     */
    public function setRelation($relation, $value);

    /**
     * Set the entire relations array on the model.
     *
     * @param  array  $relations
     * @return $this
     */
    public function setRelations(array $relations);

}