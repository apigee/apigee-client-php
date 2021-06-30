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

use Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialController;
use Apigee\Edge\Api\Management\Entity\AppInterface;
use Apigee\Edge\Api\Management\Entity\AppOwnerInterface;
use Apigee\Edge\Api\Management\Entity\DeveloperInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Api\Management\Entity\DeveloperAppTestEntityProviderTrait;
use Apigee\Edge\Tests\Api\Management\Entity\DeveloperTestEntityProviderTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationTestControllerTesterInterface;

/**
 * Class DeveloperAppCredentialControllerTest.
 *
 * @group controller
 * @group management
 */
class DeveloperAppCredentialControllerTest extends AppCredentialControllerTestBase
{
    use DeveloperControllerAwareTestTrait;
    use DeveloperAppControllerAwareTestTrait;
    use DeveloperTestEntityProviderTrait;
    use DeveloperAppTestEntityProviderTrait;

    /**
     * {@inheritdoc}
     */
    protected static function entityController(ClientInterface $client = null): EntityControllerTesterInterface
    {
        $client = $client ?? static::defaultAPIClient();

        return new EntityControllerTester(new DeveloperAppCredentialController(static::defaultTestOrganization($client), static::$testAppOwner->id(), static::$testApp->id(), $client));
    }

    protected static function setupTestApp(AppOwnerInterface $appOwner): AppInterface
    {
        $app = static::getNewDeveloperApp();
        static::developerAppController()->create($app);

        return $app;
    }

    protected static function setupTestAppOwner(): AppOwnerInterface
    {
        $developer = static::getNewDeveloper();
        static::developerController()->create($developer);

        return $developer;
    }

    protected static function developerAppControllerDeveloperAppOwner(): DeveloperInterface
    {
        return static::$testAppOwner;
    }

    protected static function entityCreateOperationTestController(): EntityCreateOperationTestControllerTesterInterface
    {
        return new EntityCreateOperationControllerTester(static::developerAppController());
    }

    protected static function getNewEntity(): EntityInterface
    {
        return static::getNewDeveloperApp();
    }

    protected static function appByOwnerController(): EntityControllerTesterInterface
    {
        return static::developerAppController();
    }
}
