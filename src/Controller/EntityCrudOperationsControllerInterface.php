<?php

namespace Apigee\Edge\Controller;

use Apigee\Edge\Entity\EntityInterface;

/**
 * Interface EntityCrudOperationsControllerInterface.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface EntityCrudOperationsControllerInterface
{
    /**
     * Loads an entity by ID from Edge.
     *
     * @param string $entityId
     *
     * @return EntityInterface
     */
    public function load(string $entityId): EntityInterface;

    /**
     * Creates an entity to Edge.
     *
     * @param EntityInterface $entity
     */
    public function create(EntityInterface $entity): void;

    /**
     * Updates an entity to Edge.
     *
     * @param EntityInterface $entity
     */
    public function update(EntityInterface $entity): void;

    /**
     * Removes an entity from Edge.
     *
     * @param string $entityId
     *
     * @return EntityInterface
     */
    public function delete(string $entityId): EntityInterface;
}
