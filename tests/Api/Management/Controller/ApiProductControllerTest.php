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

use Apigee\Edge\Api\Management\Entity\ApiProduct;
use Apigee\Edge\Api\Management\Entity\ApiProductInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Tests\Api\Management\Entity\ApiProductTestEntityProviderTrait;
use Apigee\Edge\Tests\Api\Management\Entity\ParameterUrlEncodingTestTrait;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationTestControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityDeleteOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityLoadOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityUpdateOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\TestClientFactory;

/**
 * Class ApiProductControllerTest.
 *
 * @group controller
 * @group management
 */
class ApiProductControllerTest extends EntityControllerTestBase
{
    use ApiProductControllerAwareTestTrait;
    use ApiProductTestEntityProviderTrait;
    use DefaultAPIClientAwareTrait;
    use ParameterUrlEncodingTestTrait;
    // The order of these trait matters. Check @depends in test methods.
    use EntityCreateOperationControllerTestTrait;
    use EntityLoadOperationControllerTestTrait;
    use EntityUpdateOperationControllerTestTrait;
    use EntityDeleteOperationControllerTestTrait;
    use PaginatedEntityListingControllerTestTraitBase;
    use PaginatedEntityIdListingControllerTestTrait;
    use PaginatedEntityListingControllerTestTrait;
    use AttributesAwareEntityControllerTestTrait;

    public function testSearchByAttribute(): void
    {
        /** @var \Apigee\Edge\Api\Management\Controller\ApiProductControllerInterface $controller */
        $controller = static::entityController();
        if (TestClientFactory::isOfflineClient(static::defaultAPIClient())) {
            $expectedEntityId = 'phpunit_test';
            $unexpectedId = 'should_not_appear';
        } else {
            /** @var ApiProductInterface $unexpectedEntity */
            $unexpectedEntity = static::getNewEntity();
            $unexpectedEntity->setAttribute('foo', 'foo');
            // Use the same controller as the entity create test uses because
            // it could be different than what static::entityController();
            // returns.
            static::controllerForEntityCreate()->create($unexpectedEntity);
            $unexpectedId = $unexpectedEntity->id();
            /** @var ApiProductInterface $expectedEntity */
            $expectedEntity = static::getNewEntity();
            $expectedEntity->setAttribute('foo', 'bar');
            static::controllerForEntityCreate()->create($expectedEntity);
            $expectedEntityId = $expectedEntity->id();
        }

        $ids = $controller->searchByAttribute('foo', 'bar');
        $this->assertContains($expectedEntityId, $ids);
        $this->assertNotContains($unexpectedId, $ids);
    }

    /**
     * {@inheritdoc}
     */
    protected static function entityController(?ClientInterface $client = null): EntityControllerTesterInterface
    {
        return static::apiProductController($client);
    }

    /**
     * {@inheritdoc}
     */
    protected static function getNewEntity(): EntityInterface
    {
        return static::getNewApiProduct(!TestClientFactory::isOfflineClient(static::defaultAPIClient()));
    }

    /**
     * {@inheritdoc}
     */
    protected function entityForUpdateTest(EntityInterface $existing): EntityInterface
    {
        /* @var \Apigee\Edge\Api\Management\Entity\ApiProductInterface $existing */
        return static::getUpdatedApiProduct($existing, !TestClientFactory::isOfflineClient(static::defaultAPIClient()));
    }

    /**
     * {@inheritdoc}
     */
    protected static function entityCreateOperationTestController(): EntityCreateOperationTestControllerTesterInterface
    {
        return new EntityCreateOperationControllerTester(static::entityController());
    }

    /**
     * {@inheritdoc}
     */
    protected static function getEntityToTestUrlEncoding(): EntityInterface
    {
        // Use a name that makes use of URL encoding to test that CRUD works with encoding (a space in this case).
        return new ApiProduct([
            'name' => static::randomGenerator()->machineName() . ' ' . static::randomGenerator()->machineName(),
            'displayName' => static::randomGenerator()->displayName(),
            'approvalType' => ApiProductInterface::APPROVAL_TYPE_AUTO,
            'attributes' => new AttributesProperty(['foo' => 'bar']),
            'scopes' => ['scope 1', 'scope 2'],
            'quota' => static::randomGenerator()->number(),
            'quotaInterval' => static::randomGenerator()->number(),
            'quotaTimeUnit' => ApiProductInterface::QUOTA_INTERVAL_MINUTE,
        ]);
    }
}
