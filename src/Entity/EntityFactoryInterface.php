<?php

namespace Apigee\Edge\Entity;

/**
 * Interface EntityFactoryInterface.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface EntityFactoryInterface
{
    /**
     * Returns the FQCN of the entity class that belongs to an entity controller.
     *
     * @param string|\Apigee\Edge\Controller\AbstractEntityController $entityController
     *   Fully qualified class name or an object.
     *
     * @return string
     */
    public function getEntityTypeByController($entityController): string;

    /**
     * Returns a new instance of entity that belongs to an entity controller.
     *
     * @param string|\Apigee\Edge\Controller\AbstractEntityController $entityController
     *   Fully qualified class name or an object.
     *
     * @return \Apigee\Edge\Entity\EntityInterface
     */
    public function getEntityByController($entityController): EntityInterface;
}
