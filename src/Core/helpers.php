<?php

if(!function_exists('hermes'))
{

    function hermes($abstract = null, array $parameters = [])
    {

        if(defined('LARAVEL_START')){
            return app($abstract, $parameters);
        }
        else
        {
            if(is_null($abstract)) {
                return \Illuminate\Container\Container::getInstance();
            }

            return empty($parameters)
                ? \Illuminate\Container\Container::getInstance()->make($abstract)
                : \Illuminate\Container\Container::getInstance()->makeWith($abstract, $parameters);
        }
    }

}