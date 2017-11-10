<?php

namespace Apigee\Edge\Entity;

/**
 * Interface EntityFactoryInterface.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface EntityFactoryInterface
{
    public function getEntityByController(BaseEntityControllerInterface $entityController): EntityInterface;
}
