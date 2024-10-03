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

use Apigee\Edge\Controller\PaginatedEntityListingControllerInterface;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerAwareTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\TestClientFactory;
use InvalidArgumentException;
use PHPUnit\Framework\Assert;

trait PaginatedEntityListingControllerTestTrait
{
    use DefaultAPIClientAwareTrait;
    use EntityControllerAwareTestTrait;

    /**
     * @depends testPaginatedEntityIdListing
     *
     * Not necessary to depend on this test, but if that fails this one should
     * fail as well.
     */
    public function testPaginatedAllEntityListing(): void
    {
        $randomPrefix = $this->entityIdPrefixForPaginatedEntityListingTest();
        if (TestClientFactory::isOfflineClient(static::defaultAPIClient())) {
            $created = $this->controllerForPaginatedEntityListing()->getEntities();
        } else {
            $created = $this->setupForPaginatedEntityListingTest($randomPrefix);
        }
        $entities = $this->controllerForPaginatedEntityListing()->getEntities();
        // We created 5 entities so at least 5 entities should be returned.
        Assert::assertTrue(count($entities) >= count($created));
    }

    /**
     * @depends testPaginatedAllEntityListing
     *
     * Not necessary to depend on this test, but if that fails this one should
     * fail as well.
     */
    public function testPaginatedEntityListing(): void
    {
        $randomPrefix = $this->entityIdPrefixForPaginatedEntityListingTest();
        if (TestClientFactory::isOfflineClient(static::defaultAPIClient())) {
            $entities = $this->controllerForPaginatedEntityListing()->getEntities();
        } else {
            $entities = $this->setupForPaginatedEntityListingTest($randomPrefix);
        }
        /** @var \Apigee\Edge\Entity\EntityInterface[] $tmp */
        $tmp = $entities;
        // Get the 4th item from the list.
        array_shift($tmp);
        array_shift($tmp);
        array_shift($tmp);
        $entityId = $this->entityIdShouldBeUsedInPagination(array_shift($tmp));
        $limit = 2;
        $pager = $this->controllerForPaginatedEntityListing()->createPager($limit, $entityId);
        $result = $this->controllerForPaginatedEntityListing()->getEntities($pager);
        $this->validatePaginatedEntityListingResult($limit, $entityId, $result, $entities);
    }

    /**
     * @param int $limit
     * @param string $startKey
     * @param \Apigee\Edge\Entity\EntityInterface[] $result
     * @param \Apigee\Edge\Entity\EntityInterface[] $entities
     */
    protected function validatePaginatedEntityListingResult(int $limit, string $startKey, array $result, array $entities): void
    {
        /** @var \Apigee\Edge\Entity\EntityInterface $firstInResult */
        $firstInResult = reset($result);
        Assert::assertEquals($startKey, $this->entityIdShouldBeUsedInPagination($firstInResult));
        Assert::assertEquals($entities[$this->entityIdShouldBeUsedInPagination($firstInResult)], $firstInResult);
        Assert::assertCount($limit, $result);
    }

    /**
     * @param string $entityIdPrefix
     *   Random entity id prefix for this test.
     *
     * @return string[]
     */
    abstract protected function setupForPaginatedEntityListingTest(string $entityIdPrefix): array;

    abstract protected function entityIdPrefixForPaginatedEntityListingTest(): string;

    /**
     * Controller for paginated entity listing operation testing.
     *
     * @return EntityControllerTesterInterface|PaginatedEntityListingControllerInterface
     */
    protected static function controllerForPaginatedEntityListing(): EntityControllerTesterInterface
    {
        $controller = static::entityController();

        if ($controller->instanceOf(PaginatedEntityListingControllerInterface::class)) {
            throw new InvalidArgumentException('Controller must implements PaginatedEntityListingControllerInterface.');
        }

        return $controller;
    }
}
