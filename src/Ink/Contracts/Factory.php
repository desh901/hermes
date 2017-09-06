<?php

namespace Hermes\Ink\Contracts;


interface Factory
{

    /**
     * Make a fake instance of a factory
     *
     * @param string $entity
     * @param int $statusCode
     * @param array $responseBody
     *
     * @return
     */
    public function fake($entity, $statusCode, array $responseBody);

    /**
     * Get an action instance
     *
     * @param string $entity
     * @return
     */
    public function make($entity);

}