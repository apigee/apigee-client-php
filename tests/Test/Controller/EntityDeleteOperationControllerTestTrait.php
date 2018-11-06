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

use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Exception\ApiRequestException;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerAwareTestTrait;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerValidatorAwareTrait;
use Apigee\Edge\Tests\Test\Utility\EntityStorage;
use Apigee\Edge\Tests\Test\Utility\MarkOnlineTestSkippedAwareTrait;
use PHPUnit\Framework\Assert;

/**
 * Validates controllers that support entity load operations.
 *
 * @see \Apigee\Edge\Controller\EntityLoadOperationControllerInterface
 */
trait EntityDeleteOperationControllerTestTrait
{
    use EntityControllerAwareTestTrait;
    use EntitySerializerAwareTestTrait;
    use EntitySerializerValidatorAwareTrait;
    use MarkOnlineTestSkippedAwareTrait;

    /**
     * @depends testUpdate
     * @group online
     *
     * @param \Apigee\Edge\Entity\EntityInterface $entity
     */
    public function testDelete(EntityInterface $entity): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        // There is nothing to validate the entity returned in response.
        static::controllerForEntityDelete()->delete($entity->id());
        EntityStorage::getInstance()->removeEntity($entity->id(), static::entityController());
        $this->validateDeletedEntity($entity);
    }

    protected function validateDeletedEntity(EntityInterface $entity): void
    {
        // Ensure the deleted entity actually got deleted.
        try {
            static::controllerForEntityLoad()->load($entity->id());
        } catch (ApiRequestException $e) {
            Assert::assertContains('does not exist', $e->getMessage(), $e->getMessage());
        }
    }

    /**
     * Controller for entity load operation testing.
     *
     * @return \Apigee\Edge\Tests\Test\Controller\EntityDeleteOperationControllerTesterInterface
     */
    protected static function controllerForEntityDelete(): EntityDeleteOperationControllerTesterInterface
    {
        /** @var \Apigee\Edge\Controller\EntityDeleteOperationControllerInterface $controller */
        $controller = static::entityController();

        return new EntityDeleteOperationControllerTester($controller);
    }

    /**
     * Provided by EntityLoadOperationControllerTestTrait out of the box.
     *
     * @return \Apigee\Edge\Tests\Test\Controller\EntityLoadOperationControllerTesterInterface
     */
    abstract protected static function controllerForEntityLoad(): EntityLoadOperationControllerTesterInterface;
}
