<?php

/*
 * Copyright 2021 Google LLC
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

namespace Apigee\Edge\Tests\Api\ApigeeX\Controller;

use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityLoadOperationControllerTestTrait as BaseEntityLoadOperationControllerTestTrait;

/**
 * Monetization API version of EntityLoadOperationControllerTestTrait.
 */
trait EntityLoadOperationControllerTestTrait
{
    use DefaultAPIClientAwareTrait;
    use BaseEntityLoadOperationControllerTestTrait {
        alterObjectsBeforeCompareExpectedAndLoadedEntity as private overriddenAlterObjectsBeforeCompareExpectedAndLoadedEntity;
    }

    /**
     * {@inheritdoc}
     */
    public function testLoad($created = null): EntityInterface
    {
        $id = $created ?? 'phpunit';
        if ($created instanceof EntityInterface) {
            $id = $created->id();
        }
        $entity = static::controllerForEntityLoad()->load($id);
        $this->validateLoadedEntity($entity, $entity);

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
        $expected->setCreatedAt($actual->getCreatedAt());
        $expectedAsObject = json_decode($this->entitySerializer()->serialize($expected, 'json'));

        // Validate that we got back the same object as we created.
        $this->entitySerializerValidator()->validate($expectedAsObject, $actual);
    }

    protected function alterObjectsBeforeCompareExpectedAndLoadedEntity(\stdClass &$expectedAsObject, EntityInterface $loaded): void
    {
        // The serialized version of the created (actual) would not be the
        // same as the loaded entity, because entity reference properties
        // like organization profile, would be serialized differently (only
        // its ID would be kept.)
        $expectedAsObject = json_decode((string) static::defaultAPIClient()->getJournal()->getLastResponse()->getBody());
    }
}
