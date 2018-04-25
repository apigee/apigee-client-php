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

/**
 * Class NonCpsLimitEntityControllerValidator.
 *
 * Helps in validation of all entity controllers that implements NonCpsLimitEntityControllerInterface.
 *
 *
 * @see \Apigee\Edge\Controller\NonCpsListingEntityControllerInterface
 */
abstract class NonCpsLimitEntityControllerValidator extends EntityCrudOperationsControllerValidator
{
    use EntityIdsListingControllerValidatorTrait;

    /**
     * @depends testCreate
     */
    public function testGetEntities(): void
    {
        /** @var \Apigee\Edge\Controller\NonCpsListingEntityControllerInterface $controller */
        $controller = $this->getEntityController();
        $entities = $controller->getEntities();
        $this->assertNotEmpty($entities);
        /** @var \Apigee\Edge\Entity\EntityInterface $entity */
        foreach ($entities as $entity) {
            $this->assertEntityHasAllPropertiesSet($entity);
            if ($entity->id() == static::expectedAfterEntityCreate()->id()) {
                $this->assertArraySubset(
                    array_filter(static::$objectNormalizer->normalize(static::expectedAfterEntityCreate())),
                    static::$objectNormalizer->normalize($entity)
                );
            }
        }
    }
}
