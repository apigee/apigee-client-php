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

use Apigee\Edge\Api\Management\Controller\EnvironmentController;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\DefaultTestOrganizationAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationTestControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityLoadOperationControllerTestTrait;

/**
 * Class EnvironmentControllerTest.
 *
 * CUD operations are only available in on-prem (private cloud) installation
 * of Apigee Edge so we did not test them at the moment when this test was
 * written.
 *
 * @group controller
 * @group management
 */
class EnvironmentControllerTest extends EntityControllerTestBase
{
    use DefaultAPIClientAwareTrait;
    use DefaultTestOrganizationAwareTrait;
    use EntityLoadOperationControllerTestTrait;
    use NonPaginatedEntityIdListingControllerTestTrait;

    /**
     * Test load depends on this so we had to implement it.
     *
     * @return \Apigee\Edge\Entity\EntityInterface
     */
    public function testCreate(): EntityInterface
    {
        /** @var \Apigee\Edge\Api\Management\Controller\EnvironmentControllerInterface $controller */
        $controller = static::entityController();
        $entity = $controller->load('test');
        $this->assertNotNull($entity);

        return $entity;
    }

    /**
     * @inheritdoc
     */
    protected static function entityController(ClientInterface $client = null): EntityControllerTesterInterface
    {
        $client = $client ?? static::defaultAPIClient();

        return new EntityControllerTester(new EnvironmentController(static::defaultTestOrganization($client), $client));
    }

    /**
     * @inheritdoc
     */
    protected static function entityCreateOperationTestController(): EntityCreateOperationTestControllerTesterInterface
    {
        /** @var \Apigee\Edge\Api\Management\Controller\EnvironmentControllerInterface $controller */
        $controller = static::entityController();

        return new EntityCreateOperationControllerTester($controller);
    }
}
