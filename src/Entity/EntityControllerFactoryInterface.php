<?php

namespace Apigee\Edge\Entity;

/**
 * Interface EntityControllerFactoryInterface.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface EntityControllerFactoryInterface
{
    /**
     * @param string $path
     * @return BaseEntityControllerInterface
     *
     * @throws \Apigee\Edge\Exception\UnknownEndpointException
     */
    public function getControllerByEndpoint(string $path): BaseEntityControllerInterface;
}
