<?php

namespace Apigee\Edge\Entity;

/**
 * Interface NonCpsEntityControllerInterface.
 *
 * For those entities that does not support CPS limits, like the Organizations.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface NonCpsEntityControllerInterface extends BaseEntityControllerInterface
{

    /**
     * Returns list of entities from Edge. The returned number of entities can _not_ be limited.
     *
     * @return BaseEntityControllerInterface[]
     * @see \Apigee\Edge\Entity\EntityController::getEntities()
     */
    public function getEntities(): array;

    /**
     * Returns list of entity ids from Edge. The returned number of entities can _not_ be limited.
     *
     * @return array
     * @see \Apigee\Edge\Entity\EntityController::getEntityIds()
     */
    public function getEntityIds(): array;
}
