<?php

/*
 * Copyright 2023 Google LLC
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
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationTestControllerAwareTrait;
use Apigee\Edge\Tests\Test\Entity\NewEntityProviderTrait;
use Apigee\Edge\Tests\Test\Utility\RandomGeneratorAwareTrait;

trait PaginatedEntityListingControllerTestTraitBase
{
    use EntityCreateOperationTestControllerAwareTrait;
    use NewEntityProviderTrait;
    use RandomGeneratorAwareTrait;

    /**
     * @param string $entityIdPrefix
     *   Random entity id prefix for this test.
     *
     * @return \Apigee\Edge\Entity\EntityInterface[]
     */
    protected function setupForPaginatedEntityListingTest(string $entityIdPrefix): array
    {
        static $entities = [];
        // Do not create test entities unnecessarily, if we have already created
        // them let's re-use them.
        if (!array_key_exists($entityIdPrefix, $entities)) {
            for ($i = 1; $i <= 5; ++$i) {
                $entity = static::getNewEntity();
                $setter = 'set' . ucfirst($entity::idProperty());
                // Make sure that all test entities created for this has a very
                // unique id.
                $entity->{$setter}($entityIdPrefix . '_' . $entity->id());
                $this->entityCreateOperationTestController()->create($entity);
                // Use the proper id as key in the returned array.
                $entities[$entityIdPrefix][$this->entityIdShouldBeUsedInPagination($entity)] = $entity;
            }
        }

        return $entities[$entityIdPrefix];
    }

    /**
     * Some entities have multiple primary (rather say unique) ids at a time.
     *
     * This helper function allows to choose the best one for pagination
     * testing.
     *
     * @param \Apigee\Edge\Entity\EntityInterface $entity
     *
     * @return string
     */
    protected function entityIdShouldBeUsedInPagination(EntityInterface $entity): string
    {
        return $entity->id();
    }

    /**
     * Returns the same random prefix for paginated entity tests.
     *
     * @return string
     */
    protected function entityIdPrefixForPaginatedEntityListingTest(): string
    {
        static $randomPrefix;
        if (null === $randomPrefix) {
            $randomPrefix = static::randomGenerator()->number();
        }

        return $randomPrefix;
    }
}
