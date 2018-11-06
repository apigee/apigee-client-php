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

use Apigee\Edge\Controller\EntityLoadOperationControllerInterface;
use Apigee\Edge\Entity\EntityInterface;

final class EntityLoadOperationControllerTester extends EntityControllerTesterBase implements EntityLoadOperationControllerTesterInterface
{
    /**
     * @inheritDoc
     */
    public function load(string $entityId): EntityInterface
    {
        return $this->decorated->load($entityId);
    }

    /**
     * @inheritDoc
     */
    protected function validateController($controller): void
    {
        parent::validateController($controller);
        if (!$controller instanceof EntityLoadOperationControllerInterface && !$controller instanceof EntityControllerTesterInterface) {
            $class = get_class($controller);
            throw new \InvalidArgumentException("Controller must implement EntityLoadOperationControllerInterface or EntityControllerTesterInterface. Got {$class}.");
        }
    }
}
