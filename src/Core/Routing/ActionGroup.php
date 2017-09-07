<?php

namespace Hermes\Core\Routing;

use Illuminate\Support\Arr;

class ActionGroup
{
    /**
     * Merge action groups into a new array.
     *
     * @param  array  $new
     * @param  array  $old
     * @return array
     */
    public static function merge($new, $old)
    {
        if (isset($new['base_url'])) {
            unset($old['base_url']);
        }

        if(isset($new['timeout'])) {
            unset($old['timeout']);
        }

        $new = array_merge(static::formatAs($new, $old), [
            'namespace' => static::formatNamespace($new, $old),
            'prefix' => static::formatPrefix($new, $old),
        ]);

        return array_merge_recursive(Arr::except(
            $old, ['namespace', 'prefix', 'as']
        ), $new);
    }

    /**
     * Format the namespace for the new group attributes.
     *
     * @param  array  $new
     * @param  array  $old
     * @return string|null
     */
    protected static function formatNamespace($new, $old)
    {
        if (isset($new['namespace'])) {
            return isset($old['namespace'])
                ? trim($old['namespace'], '\\').'\\'.trim($new['namespace'], '\\')
                : trim($new['namespace'], '\\');
        }

        return isset($old['namespace']) ? $old['namespace'] : null;
    }

    /**
     * Format the prefix for the new group attributes.
     *
     * @param  array  $new
     * @param  array  $old
     * @return string|null
     */
    protected static function formatPrefix($new, $old)
    {
        $old = Arr::get($old, 'prefix');

        return isset($new['prefix']) ? trim($old, '/').'/'.trim($new['prefix'], '/') : $old;
    }

    /**
     * Format the "as" clause of the new group attributes.
     *
     * @param  array  $new
     * @param  array  $old
     * @return array
     */
    protected static function formatAs($new, $old)
    {
        if (isset($old['as'])) {
            $new['as'] = $old['as'].Arr::get($new, 'as', '');
        }

        if(Arr::has($new, 'as')) $new['as'] = $new['as'].'.';

        return $new;
    }
}
