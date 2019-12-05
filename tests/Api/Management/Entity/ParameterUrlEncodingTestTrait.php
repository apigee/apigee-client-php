<?php

/*
 * Copyright 2019 Google LLC
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

namespace Apigee\Edge\Tests\Api\Management\Entity;

use Apigee\Edge\Api\Management\Entity\DeveloperInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationTestControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityLoadOperationControllerTesterInterface;
use Apigee\Edge\Tests\Test\Utility\MarkOnlineTestSkippedAwareTrait;

trait ParameterUrlEncodingTestTrait
{
    use MarkOnlineTestSkippedAwareTrait;

    /**
     * Use an entity with an ID that makes use of URL encoding to test that CRUD works with encoding.
     */
    public function testUrlEncoding(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);

        $entity = static::getEntityToTestUrlEncoding();
        $original = clone $entity;
        static::controllerForEntityCreate()->create($entity);
        $this->validateCreatedEntity($entity, $original);

        // Test the entity can be retrieved.
        $id = ($entity instanceof DeveloperInterface) ? $entity->getEmail() : $entity->id();
        $loaded = static::controllerForEntityLoad()->load($id);
        $this->validateLoadedEntity($entity, $loaded);
    }

    /**
     * Return an entity with an ID that makes use of URL encoding.
     *
     * @return EntityInterface
     */
    abstract protected static function getEntityToTestUrlEncoding(): EntityInterface;

    abstract protected static function controllerForEntityCreate(): EntityCreateOperationTestControllerTesterInterface;

    abstract protected static function controllerForEntityLoad(): EntityLoadOperationControllerTesterInterface;
}
