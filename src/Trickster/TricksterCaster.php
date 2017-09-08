<?php

namespace Hermes\Trickster;


use Symfony\Component\VarDumper\Caster\Caster;

class TricksterCaster
{

    /**
     * Application properties to include in the presenter
     *
     * @var array
     */

    private static $appProperties = [
        'path',
        'basePath',
        'configPath',
        'langPath',
        'version'
    ];

    /**
     * Get an array representing the properties of the Hermes application
     *
     * @param \Hermes\Core\Application $app
     * @return array
     */
    public static function castApplication($app)
    {
        $results = [];

        foreach (self::$appProperties as $property) {

            try {
                $val = $app->$property();

                if(!is_null($val)) {
                    $results[Caster::PREFIX_VIRTUAL.$property] = $val;
                }
            } catch (\Exception $e) {
                //
            }

        }

        return $results;

    }

    /**
     * Get an array representing the properties of a collection
     *
     * @param \Illuminate\Support\Collection $collection
     * @return array
     */
    public static function castCollection($collection)
    {

        return [
            Caster::PREFIX_VIRTUAL.'all' => $collection->all()
        ];

    }

    /**
     * Get an array representing the properties of an object
     *
     * @param \Hermes\Ink\Object $object
     * @return array
     */
    public static function castObject($object)
    {
        $attributes = $object->toArray(false);

        $visible = array_flip(
            $object->getVisible() ?: array_diff(array_keys($attributes), $object->getHidden())
        );

        $results = [];

        foreach(array_intersect_key($attributes, $visible) as $key => $value)
        {
            $results[(isset($visible[$key]) ? Caster::PREFIX_VIRTUAL : Caster::PREFIX_PROTECTED).$key] = $value;
        }

        return $results;
    }

    /**
     * Get an array representing the properties of an action
     *
     * @param \Hermes\Ink\Action $action
     * @return array
     */
    public static function castAction($action)
    {

        $attributes = [

            'name' => $action->getName(),
            'method' => $action->getMethod(),
            'baseUrl' => $action->getBaseUrl(),
            'uri' => $action->getEndpoint(),
            'timeout' => $action->getTimeout(),
            'parseWith' => $action->getObjectParsers(),
            'options' => $action->getOptions(),
            'payloadType' => $action->getPayloadType(),

        ];

        $results = [];

        foreach ($attributes as $attribute => $value)
        {

            $results[Caster::PREFIX_VIRTUAL.$attribute] = $value;

        }

        return $results;
    }

}