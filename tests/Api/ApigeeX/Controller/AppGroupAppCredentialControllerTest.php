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

use Apigee\Edge\Api\ApigeeX\Controller\AppGroupAppCredentialController;
use Apigee\Edge\Api\ApigeeX\Entity\AppGroup;
use Apigee\Edge\Api\ApigeeX\Entity\AppGroupInterface;
use Apigee\Edge\Api\Management\Entity\AppInterface;
use Apigee\Edge\Api\Management\Entity\AppOwnerInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Api\ApigeeX\Entity\AppGroupAppTestEntityProviderTrait;
use Apigee\Edge\Tests\Api\ApigeeX\Entity\AppGroupTestEntityProviderTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationTestControllerTesterInterface;

/**
 * Class AppGroupAppCredentialControllerTest.
 *
 * @group controller
 * @group management
 */
class AppGroupAppCredentialControllerTest extends AppCredentialControllerTestBase
{
    use AppGroupControllerAwareTestTrait;
    use AppGroupAppControllerAwareTestTrait;
    use AppGroupTestEntityProviderTrait;
    use AppGroupAppTestEntityProviderTrait;

    /**
     * {@inheritdoc}
     */
    protected static function entityController(ClientInterface $client = null): EntityControllerTesterInterface
    {
        $client = $client ?? static::defaultAPIClient();

        return new EntityControllerTester(new AppGroupAppCredentialController(static::defaultTestOrganization($client), static::$testAppOwner->id(), static::$testApp->id(), $client));
    }

    protected static function setupTestApp(AppGroup $appOwner): AppInterface
    {
        $app = static::getNewAppGroupApp();
        static::appGroupAppController()->create($app);

        return $app;
    }

    protected static function setupTestAppOwner(): AppGroup
    {
        $appGroup = static::getNewAppGroup();
        static::appGroupController()->create($appGroup);

        return $appGroup;
    }

    protected static function appGroupAppControllerAppGroupAppOwner(): AppGroupInterface
    {
        return static::$testAppOwner;
    }

    protected static function entityCreateOperationTestController(): EntityCreateOperationTestControllerTesterInterface
    {
        return new EntityCreateOperationControllerTester(static::appGroupAppController());
    }

    protected static function getNewEntity(): EntityInterface
    {
        return static::getNewAppGroupApp();
    }

    protected static function appByOwnerController(): EntityControllerTesterInterface
    {
        return static::appGroupAppController();
    }
}
