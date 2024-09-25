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

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Controller\NonPaginatedEntityIdListingControllerInterface;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerAwareTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationTestControllerAwareTrait;
use Apigee\Edge\Tests\Test\Utility\EntityStorage;
use InvalidArgumentException;
use PHPUnit\Framework\Assert;

trait NonPaginatedEntityIdListingControllerTestTrait
{
    use DefaultAPIClientAwareTrait;
    use EntityControllerAwareTestTrait;
    use EntityCreateOperationTestControllerAwareTrait;

    /**
     * @depends testCreate
     */
    public function testNonPaginatedEntityIdListing(): void
    {
        $entityIds = $this->controllerForNonPaginatedEntityIdListing()->getEntityIds();
        $this->validateNonPaginatedEntityIdListingResult($entityIds);
    }

    protected function validateNonPaginatedEntityIdListingResult(array $entityIds): void
    {
        // We should get back at least as many entities as we know that we
        // created in this test run.
        // entityCreateOperationTestController() and
        // controllerForNonPaginatedEntityIdListing() should return the same
        // objects in general, so this is fine.
        $createdEntitiesByController = EntityStorage::getInstance()->getCreatedEntitiesByStorage(static::entityCreateOperationTestController());
        Assert::assertTrue(count($entityIds) >= count($createdEntitiesByController));
    }

    /**
     * Controller for non-paginated entity id listing operation testing.
     *
     * @return EntityControllerTesterInterface|NonPaginatedEntityIdListingControllerInterface
     */
    protected static function controllerForNonPaginatedEntityIdListing(): EntityControllerTesterInterface
    {
        $controller = static::entityController();
        if ($controller->instanceOf(NonPaginatedEntityIdListingControllerInterface::class)) {
            throw new InvalidArgumentException('Controller must implements NonPaginatedEntityIdListingControllerInterface.');
        }

        return $controller;
    }
}
