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

use Apigee\Edge\Controller\PaginatedEntityIdListingControllerInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerAwareTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\TestClientFactory;
use InvalidArgumentException;
use PHPUnit\Framework\Assert;

trait PaginatedEntityIdListingControllerTestTrait
{
    use DefaultAPIClientAwareTrait;
    use EntityControllerAwareTestTrait;

    public function testPaginatedEntityIdListing(): void
    {
        $randomPrefix = $this->entityIdPrefixForPaginatedEntityListingTest();
        if (!TestClientFactory::isOfflineClient(static::defaultAPIClient())) {
            $entities = $this->setupForPaginatedEntityListingTest($randomPrefix);
            $entityIds = array_map(function (EntityInterface $entity) {
                return $this->entityIdShouldBeUsedInPagination($entity);
            }, $entities);
        } else {
            $entityIds = $this->controllerForPaginatedEntityIdListing()->getEntityIds();
        }

        /** @var \Apigee\Edge\Entity\EntityInterface[] $tmp */
        $tmp = $entityIds;
        // Get the 4th item from the list.
        array_shift($tmp);
        array_shift($tmp);
        array_shift($tmp);
        $entityId = array_shift($tmp);
        $limit = 2;
        $pager = $this->controllerForPaginatedEntityIdListing()->createPager($limit, $entityId);
        $result = $this->controllerForPaginatedEntityIdListing()->getEntityIds($pager);
        $this->validatePaginatedEntityIdListingResult($limit, $entityId, $result, $entityIds);
    }

    protected function validatePaginatedEntityIdListingResult(int $limit, string $startKey, array $result, array $entityIds): void
    {
        Assert::assertEquals($startKey, $result[0]);
        Assert::assertCount($limit, $result);
    }

    abstract protected function setupForPaginatedEntityListingTest(string $entityIdPrefix): array;

    abstract protected function entityIdPrefixForPaginatedEntityListingTest(): string;

    /**
     * Controller for paginated entity id listing operation testing.
     *
     * @return EntityControllerTesterInterface|PaginatedEntityIdListingControllerInterface
     */
    protected static function controllerForPaginatedEntityIdListing(): EntityControllerTesterInterface
    {
        $controller = static::entityController();
        if ($controller->instanceOf(PaginatedEntityIdListingControllerInterface::class)) {
            throw new InvalidArgumentException('Controller must implements PaginatedEntityIdListingControllerInterface.');
        }

        return $controller;
    }
}
