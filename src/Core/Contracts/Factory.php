<?php

namespace Hermes\Core\Contracts;


interface Factory
{


    /**
     * Returns a fake instance of an action
     *
     * @param $entity
     * @param $statusCode
     * @param array $responseBody
     * @return mixed
     */
    public function fake($entity, $statusCode, array $responseBody);

    /**
     * Instantiates an action
     *
     * @param $entity
     * @return mixed
     */
    public function make($entity);


}