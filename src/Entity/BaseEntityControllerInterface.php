<?php

namespace Apigee\Edge\Entity;

/**
 * Interface BaseEntityControllerInterface.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface BaseEntityControllerInterface
{
    /**
     * Loads an entity by ID from Edge.
     *
     * @param string $entityId
     * @return EntityInterface
     */
    public function load(string $entityId): EntityInterface;

    /**
     * Saves (create or update) an entity to Edge.
     *
     * @param EntityInterface $entity
     * @return EntityInterface
     */
    public function save(EntityInterface $entity): EntityInterface;

    /**
     * Removes an entity from Edge.
     *
     * @param string $entityId
     * @return EntityInterface
     */
    public function delete(string $entityId): EntityInterface;
}
