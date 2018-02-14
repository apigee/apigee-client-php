<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Controller;

use Apigee\Edge\Entity\EntityInterface;

/**
 * Interface EntityCrudOperationsControllerInterface.
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
