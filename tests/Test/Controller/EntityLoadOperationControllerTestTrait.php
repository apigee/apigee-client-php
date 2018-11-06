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
use Apigee\Edge\Entity\Property\DescriptionPropertyInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerAwareTestTrait;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerValidatorAwareTrait;

/**
 * Validates controllers that support entity load operations.
 *
 * @see \Apigee\Edge\Controller\EntityLoadOperationControllerInterface
 */
trait EntityLoadOperationControllerTestTrait
{
    use EntityControllerAwareTestTrait;
    use EntitySerializerAwareTestTrait;
    use EntitySerializerValidatorAwareTrait;

    /**
     * @depends testCreate
     *
     * @param \Apigee\Edge\Entity\EntityInterface $created
     */
    public function testLoad(EntityInterface $created): EntityInterface
    {
        $entity = static::controllerForEntityLoad()->load($created->id());
        $this->validateLoadedEntity($created, $entity);

        return $entity;
    }

    protected function validateLoadedEntity(EntityInterface $expected, EntityInterface $actual): void
    {
        // Tricky Management API does not return "description" property
        // if it is not set in create but it returns an empty string when
        // it gets loaded.
        if ($expected instanceof DescriptionPropertyInterface && $actual instanceof DescriptionPropertyInterface && null !== $actual->getDescription()) {
            $expected->setDescription($actual->getDescription());
        }
        $expectedAsObject = json_decode($this->entitySerializer()->serialize($expected, 'json'));
        $this->alterObjectsBeforeCompareExpectedAndLoadedEntity($expectedAsObject, $actual);
        // Validate that we got back the same object as we created.
        $this->entitySerializerValidator()->validate($expectedAsObject, $actual);
    }

    protected function alterObjectsBeforeCompareExpectedAndLoadedEntity(\stdClass &$expectedAsObject, EntityInterface $loaded): void
    {
    }

    /**
     * Controller for entity load operation testing.
     *
     * @return \Apigee\Edge\Tests\Test\Controller\EntityLoadOperationControllerTesterInterface
     */
    protected static function controllerForEntityLoad(): EntityLoadOperationControllerTesterInterface
    {
        /** @var \Apigee\Edge\Controller\EntityLoadOperationControllerInterface $controller */
        $controller = static::entityController();

        return new EntityLoadOperationControllerTester($controller);
    }
}
