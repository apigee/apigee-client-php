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

use Apigee\Edge\Api\Management\Controller\AppByOwnerController;
use Apigee\Edge\Api\Management\Controller\DeveloperAppController;
use Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialController;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Exception\ApiRequestException;
use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\Tests\Test\TestClientFactory;

/**
 * Class DeveloperAppCredentialControllerTest.
 *
 * @group controller
 */
class DeveloperAppCredentialControllerTest extends AppCredentialControllerBase
{
    use DeveloperAwareControllerTestTrait;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::setupDeveloper();

        try {
            $controller = static::getAppController();
            try {
                // We have to keep a copy of phpunit@example.com developer's data because of this for offline tests.
                // See: offline-test-data/v1/organizations/phpunit/developers/phpunit@example.com .
                $entity = $controller->load(static::getAppSampleDataForEntityCreate()->id());
                static::$appName = $entity->id();
            } catch (ClientErrorException $e) {
                if ($e->getEdgeErrorCode() && 'developer.service.AppDoesNotExist' === $e->getEdgeErrorCode()) {
                    $entity = clone static::getAppSampleDataForEntityCreate();
                    $controller->create($entity);
                    static::$appName = $entity->id();
                }
            }
        } catch (ApiRequestException $e) {
            // Ensure that created test data always gets removed after an API call fails here.
            // (By default tearDownAfterClass() is not called if (any) exception occurred here.)
            static::tearDownDeveloper();
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public static function tearDownAfterClass(): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            return;
        }

        if (null === static::$appName) {
            return;
        }

        $appController = static::getAppController();
        /** @var \Apigee\Edge\Api\Management\Controller\AppCredentialControllerInterface $credentialController */
        $credentialController = static::getEntityController();
        // First, we have to delete all credentials of the created test app otherwise we are not going to be able to
        // delete the created test API product because Edge still thinks that there is an app associated with
        // the API product. After that the test API product becomes a ghost and it can not be removed without
        // additional help.
        try {
            /** @var \Apigee\Edge\Api\Management\Entity\AppInterface $app */
            $app = $appController->load(static::$appName);
            foreach ($app->getCredentials() as $credential) {
                $credentialController->delete($credential->getConsumerKey());
            }
        } catch (ClientErrorException $e) {
            // Hope that we can still remove the created test API product.
        }
        try {
            $appController->delete(static::$appName);
        } catch (ClientErrorException $e) {
            if (!$e->getEdgeErrorCode() || 'developer.service.AppDoesNotExist' !== $e->getEdgeErrorCode()) {
                printf(
                    "Unable to delete app entity with %s id.\n",
                    static::$appName
                );
            }
        }

        static::tearDownDeveloper();
        parent::tearDownAfterClass();
    }

    /**
     * @inheritdoc
     */
    protected static function getEntityController(ClientInterface $client = null): EntityControllerInterface
    {
        static $controller;
        if (!$controller) {
            $controller = new DeveloperAppCredentialController(
                static::getOrganization(static::$client),
                static::$developerId,
                static::$appName,
                static::$client
            );
        }
        static $controller;
        if (null === $client) {
            if (null === $controller) {
                $controller = new DeveloperAppCredentialController(
                    static::getOrganization(static::$client),
                    static::$developerId,
                    static::$appName,
                    static::$client
                );
            }

            return $controller;
        }

        return new DeveloperAppCredentialController(static::getOrganization($client), static::$developerId, static::$appName, $client);
    }

    protected static function getAppController(): AppByOwnerController
    {
        static $controller;
        if (!$controller) {
            $controller = new DeveloperAppController(
                static::getOrganization(static::$client),
                static::$developerId,
                static::$client
            );
        }

        return $controller;
    }

    protected static function getAppSampleDataForEntityCreate(): EntityInterface
    {
        return DeveloperAppControllerTest::sampleDataForEntityCreate();
    }
}
