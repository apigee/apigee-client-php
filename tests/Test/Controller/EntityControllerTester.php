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
use Apigee\Edge\Controller\EntityDeleteOperationControllerInterface;
use Apigee\Edge\Controller\EntityUpdateControllerOperationInterface;
use Apigee\Edge\Tests\Test\Utility\EntityStorage;

final class EntityControllerTester extends EntityControllerTesterBase implements EntityControllerTesterInterface
{
    /**
     * @inheritDoc
     */
    public function __call($name, $arguments)
    {
        $return = parent::__call($name, $arguments);

        if ('create' === $name && $this->decorated instanceof EntityCreateOperationControllerInterface) {
            EntityStorage::getInstance()->addEntity(reset($arguments), $this->decorated);
        } elseif ('update' === $name && $this->decorated instanceof EntityUpdateControllerOperationInterface) {
            EntityStorage::getInstance()->addEntity(reset($arguments), $this->decorated);
        } elseif ('delete' === $name && $this->decorated instanceof EntityDeleteOperationControllerInterface) {
            EntityStorage::getInstance()->removeEntity(reset($arguments), $this->decorated);
        }

        return $return;
    }

    /**
     * @inheritDoc
     */
    protected function validateController($controller): void
    {
        parent::validateController($controller);
        if ($controller instanceof EntityControllerTesterInterface) {
            $name = get_class($controller);
            throw new \InvalidArgumentException("A test controller should not be decorated again. Got {$name}.");
        }
    }
}
