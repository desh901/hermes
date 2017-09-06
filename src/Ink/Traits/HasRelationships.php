<?php

namespace Hermes\Ink\Traits;

use Illuminate\Support\Str;
use Hermes\Ink\Relations\HasOne;
use Hermes\Ink\Relations\HasMany;

trait HasRelationships
{
    /**
     * The loaded relationships for the model.
     *
     * @var array
     */
    protected $relations = [];

    /**
     * Define a one-to-one relationship.
     *
     * @param  string  $related
     * @param  string  $parentKey
     * @return HasOne
     */
    public function hasOne($related, $parentKey = null)
    {
        $parentKey = $parentKey ?: $this->getParentKey($related);

        return new HasOne($related, $this, $parentKey);
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $parentKey
     * @return HasMany
     */
    public function hasMany($related, $parentKey = null)
    {

        $parentKey = $parentKey ?: $this->getParentKey($related);

        return new HasMany($related, $this, $parentKey);
    }

    /**
     * Get the default parent key name for the object.
     *
     * @param string $related
     * @return string
     */
    public function getParentKey($related)
    {
        return Str::snake(class_basename($related));
    }

    /**
     * Get all the loaded relations for the instance.
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * Get a specified relationship.
     *
     * @param  string  $relation
     * @return mixed
     */
    public function getRelation($relation)
    {
        return $this->relations[$relation];
    }

    /**
     * Determine if the given relation is loaded.
     *
     * @param  string  $key
     * @return bool
     */
    public function relationLoaded($key)
    {
        return array_key_exists($key, $this->relations);
    }

    /**
     * Set the specific relationship in the model.
     *
     * @param  string  $relation
     * @param  mixed  $value
     * @return $this
     */
    public function setRelation($relation, $value)
    {
        $this->relations[$relation] = $value;

        return $this;
    }

    /**
     * Set the entire relations array on the model.
     *
     * @param  array  $relations
     * @return $this
     */
    public function setRelations(array $relations)
    {
        $this->relations = $relations;

        return $this;
    }

}
