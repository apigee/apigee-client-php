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

use Apigee\Edge\Api\Management\Entity\CompanyInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Api\Management\Entity\CompanyTestEntityProviderTrait;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationTestControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityDeleteOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityLoadOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityUpdateOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\TestClientFactory;
use Apigee\Edge\Tests\Test\Utility\MarkOnlineTestSkippedAwareTrait;

/**
 * Class CompanyControllerTest.
 *
 * @group controller
 * @group management
 */
class CompanyControllerTest extends EntityControllerTestBase
{
    use CompanyControllerAwareTestTrait;
    use CompanyTestEntityProviderTrait;
    use DefaultAPIClientAwareTrait;
    use MarkOnlineTestSkippedAwareTrait;
    // The order of these trait matters. Check @depends in test methods.
    use EntityCreateOperationControllerTestTrait;
    use EntityLoadOperationControllerTestTrait;
    use EntityUpdateOperationControllerTestTrait;
    use EntityDeleteOperationControllerTestTrait;
    use PaginatedEntityListingControllerTestTraitBase;
    use PaginatedEntityIdListingControllerTestTrait;
    use PaginatedEntityListingControllerTestTrait;
    use AttributesAwareEntityControllerTestTrait;

    /**
     * @group online
     */
    public function testStatusChange(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        /** @var \Apigee\Edge\Api\Management\Controller\CompanyControllerInterface $controller */
        $controller = static::entityController();

        /** @var CompanyInterface $entity */
        $entity = static::getNewEntity();
        $controller->create($entity);
        $controller->setStatus($entity->id(), CompanyInterface::STATUS_INACTIVE);
        $entity = $controller->load($entity->id());
        $this->assertEquals($entity->getStatus(), CompanyInterface::STATUS_INACTIVE);
        $controller->setStatus($entity->id(), CompanyInterface::STATUS_ACTIVE);
        $entity = $controller->load($entity->id());
        $this->assertEquals($entity->getStatus(), CompanyInterface::STATUS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    protected static function entityController(?ClientInterface $client = null): EntityControllerTesterInterface
    {
        return static::companyController($client);
    }

    /**
     * {@inheritdoc}
     */
    protected static function getNewEntity(): EntityInterface
    {
        return static::getNewCompany(!TestClientFactory::isOfflineClient(static::defaultAPIClient()));
    }

    /**
     * {@inheritdoc}
     */
    protected function entityForUpdateTest(EntityInterface $existing): EntityInterface
    {
        /* @var \Apigee\Edge\Api\Management\Entity\CompanyInterface $existing */
        return static::getUpdatedCompany($existing, !TestClientFactory::isOfflineClient(static::defaultAPIClient()));
    }

    /**
     * {@inheritdoc}
     */
    protected static function entityCreateOperationTestController(): EntityCreateOperationTestControllerTesterInterface
    {
        return new EntityCreateOperationControllerTester(static::entityController());
    }
}
