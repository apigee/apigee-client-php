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

use Apigee\Edge\Api\ApigeeX\Entity\AppGroupInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Api\ApigeeX\Entity\AppGroupAppTestEntityProviderTrait;
use Apigee\Edge\Tests\Api\ApigeeX\Entity\AppGroupTestEntityProviderTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationTestControllerTesterInterface;
use Apigee\Edge\Tests\Test\TestClientFactory;

/**
 * Class AppGroupAppControllerTest.
 *
 * @group controller
 * @group management
 */
class AppGroupAppControllerTest extends AppControllerTestBase
{
    use AppGroupAppControllerAwareTestTrait;
    use AppGroupAppTestEntityProviderTrait;
    use AppGroupControllerAwareTestTrait;
    use AppGroupTestEntityProviderTrait;

    /** @var \Apigee\Edge\Api\Management\Entity\AppGroupInterface */
    protected static $testAppGroup;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$testAppGroup = static::getNewAppGroup();
        static::appGroupController(static::defaultAPIClient())->create(static::$testAppGroup);
    }

    /**
     * {@inheritdoc}
     */
    protected static function entityController(?ClientInterface $client = null): EntityControllerTesterInterface
    {
        return static::appGroupAppController($client);
    }

    /**
     * {@inheritdoc}
     */
    protected static function getNewEntity(): EntityInterface
    {
        return static::getNewAppGroupApp([static::$testApiProduct->id()], !TestClientFactory::isOfflineClient(static::defaultAPIClient()));
    }

    /**
     * {@inheritdoc}
     */
    protected function entityForUpdateTest(EntityInterface $existing): EntityInterface
    {
        /* @var \Apigee\Edge\Api\Management\Entity\AppGroupAppInterface $existing */
        return static::getUpdatedAppGroupApp($existing, !TestClientFactory::isOfflineClient(static::defaultAPIClient()));
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
    protected static function appGroupAppControllerAppGroupAppOwner(): AppGroupInterface
    {
        return static::$testAppGroup;
    }

    /**
     * {@inheritdoc}
     */
    protected function reloadAppOwner()
    {
        return static::appGroupController()->load(static::$testAppGroup->id());
    }
}
