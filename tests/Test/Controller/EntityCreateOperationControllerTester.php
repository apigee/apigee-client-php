<?php

/*
 * Copyright 2018 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apigee\Edge\Tests\Test\Controller;

use Apigee\Edge\Controller\EntityCreateOperationControllerInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\Utility\EntityStorage;

final class EntityCreateOperationControllerTester extends EntityControllerTesterBase implements EntityCreateOperationTestControllerTesterInterface
{
    /**
     * Creates an entity in Apigee Edge.
     *
     * @param \Apigee\Edge\Entity\EntityInterface $entity
     */
    public function create(EntityInterface $entity): void
    {
        $this->decorated->create($entity);
        // The original decorator should perform this action not this one.
        if (!$this->decorated instanceof EntityControllerTesterInterface) {
            EntityStorage::getInstance()->addEntity($entity, $this->decorated);
        }
    }

    /**
     * @inheritDoc
     */
    protected function validateController($controller): void
    {
        parent::validateController($controller);
        if (!$controller instanceof EntityCreateOperationControllerInterface && !$controller instanceof EntityControllerTesterInterface) {
            $class = get_class($controller);
            throw new \InvalidArgumentException("Controller must implement EntityCreateOperationControllerInterface or EntityControllerTesterInterface. Got {$class}.");
        }
    }
}
