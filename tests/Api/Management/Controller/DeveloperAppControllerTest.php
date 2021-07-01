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

use Apigee\Edge\Api\Management\Entity\DeveloperApp;
use Apigee\Edge\Api\Management\Entity\DeveloperInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Api\Management\Entity\DeveloperAppTestEntityProviderTrait;
use Apigee\Edge\Tests\Api\Management\Entity\DeveloperTestEntityProviderTrait;
use Apigee\Edge\Tests\Api\Management\Entity\ParameterUrlEncodingTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationTestControllerTesterInterface;
use Apigee\Edge\Tests\Test\TestClientFactory;

/**
 * Class DeveloperAppControllerTest.
 *
 * @group controller
 * @group management
 */
class DeveloperAppControllerTest extends AppControllerTestBase
{
    use DeveloperAppControllerAwareTestTrait;
    use DeveloperAppTestEntityProviderTrait;
    use DeveloperControllerAwareTestTrait;
    use DeveloperTestEntityProviderTrait;
    use ParameterUrlEncodingTestTrait;

    /** @var \Apigee\Edge\Api\Management\Entity\DeveloperInterface */
    protected static $testDeveloper;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$testDeveloper = static::getNewDeveloper();
        static::developerController(static::defaultAPIClient())->create(static::$testDeveloper);
    }

    /**
     * {@inheritdoc}
     */
    protected static function entityController(ClientInterface $client = null): EntityControllerTesterInterface
    {
        return static::developerAppController($client);
    }

    /**
     * {@inheritdoc}
     */
    protected static function getNewEntity(): EntityInterface
    {
        return static::getNewDeveloperApp([static::$testApiProduct->id()], !TestClientFactory::isOfflineClient(static::defaultAPIClient()));
    }

    /**
     * {@inheritdoc}
     */
    protected function entityForUpdateTest(EntityInterface $existing): EntityInterface
    {
        /* @var \Apigee\Edge\Api\Management\Entity\DeveloperAppInterface $existing */
        return static::getUpdatedDeveloperApp($existing, !TestClientFactory::isOfflineClient(static::defaultAPIClient()));
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
    protected static function developerAppControllerDeveloperAppOwner(): DeveloperInterface
    {
        return static::$testDeveloper;
    }

    /**
     * {@inheritdoc}
     */
    protected function reloadAppOwner()
    {
        return static::developerController()->load(static::$testDeveloper->id());
    }

    /**
     * {@inheritdoc}
     */
    protected static function getEntityToTestUrlEncoding(): EntityInterface
    {
        // Use a name that makes use of URL encoding to test that CRUD works with encoding (a space in this case).
        $entity = new DeveloperApp([
            'name' => static::randomGenerator()->machineName() . ' ' . static::randomGenerator()->machineName(),
            'apiProducts' => [static::$testApiProduct->id()],
            'callbackUrl' => 'http://example.com',
        ]);
        $entity->setDisplayName(static::randomGenerator()->displayName());

        return $entity;
    }
}
