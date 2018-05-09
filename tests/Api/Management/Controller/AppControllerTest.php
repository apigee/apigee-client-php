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

use Apigee\Edge\Api\Management\Controller\AppController;
use Apigee\Edge\Api\Management\Controller\DeveloperAppController;
use Apigee\Edge\Api\Management\Entity\App;
use Apigee\Edge\Api\Management\Entity\DeveloperAppInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Exception\ApiRequestException;
use Apigee\Edge\Tests\Test\Controller\EntityControllerValidator;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;
use Apigee\Edge\Tests\Test\TestClientFactory;

/**
 * Class AppControllerTest.
 *
 * @group controller
 */
class AppControllerTest extends EntityControllerValidator
{
    use DeveloperAwareControllerTestTrait;
    use OrganizationAwareEntityControllerValidatorTrait;

    /** @var \Apigee\Edge\Api\Management\Entity\DeveloperAppInterface[] */
    protected static $createdDeveloperApps = [];

    /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppControllerInterface */
    protected static $developerAppController;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        try {
            parent::setUpBeforeClass();
            static::setupDeveloper();

            /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppController $developerAppController */
            static::$developerAppController = new DeveloperAppController(
                static::getOrganization(static::$client),
                static::$developerId,
                static::$client
            );
            /** @var \Apigee\Edge\Api\Management\Entity\DeveloperAppInterface $sampleEntity */
            $sampleEntity = clone DeveloperAppControllerBase::sampleDataForEntityCreate();
            $idField = $sampleEntity->idProperty();
            /** @var DeveloperAppInterface[] $testDeveloperApps */
            $testDeveloperApps = [$sampleEntity];
            for ($i = 1; $i <= 5; ++$i) {
                $testDeveloperApps[$i] = clone $sampleEntity;
                $testDeveloperApps[$i]->{'set' . $idField}($i . $sampleEntity->id());
            }
            // Create test data on the server or do not do anything if an offline client is in use.
            if (!TestClientFactory::isMockClient(static::$client)) {
                $i = 0;
                foreach ($testDeveloperApps as $item) {
                    /** @var \Apigee\Edge\Api\Management\Entity\AppInterface $item */
                    /** @var \Apigee\Edge\Api\Management\Entity\AppInterface $tmp */
                    static::$developerAppController->create($item);
                    if ($i % 2) {
                        static::$developerAppController->setStatus($item->id(), AppController::STATUS_REVOKE);
                        // Get the updated entity from Edge.
                        $item = static::$developerAppController->load($item->id());
                    }
                    static::$createdDeveloperApps[$item->getAppId()] = $item;
                    ++$i;
                }
            } else {
                // Ensure that testLoadApp() can be executed as an offline test.
                static::$createdDeveloperApps[$sampleEntity->getAppId()] = static::$developerAppController->load(
                    $sampleEntity->id()
                );
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
        parent::tearDownAfterClass();

        if (TestClientFactory::isMockClient(static::$client)) {
            return;
        }

        // Remove created apps on Apigee Edge.
        try {
            foreach (static::$createdDeveloperApps as $entity) {
                static::$developerAppController->delete($entity->id());
                unset(static::$createdDeveloperApps[$entity->getAppId()]);
            }
        } catch (\Exception $e) {
            printf("Unable to delete %s entity with %s id.\n", strtolower(get_class($entity)), $entity->id());
        }

        static::tearDownDeveloper();
    }

    public function testLoadApp(): void
    {
        /** @var \Apigee\Edge\Api\Management\Controller\AppControllerInterface $controller */
        $controller = $this->getEntityController();
        $firstEntity = reset(static::$createdDeveloperApps);
        $entity = $controller->loadApp($firstEntity->getAppId());
        $this->assertContains(DeveloperAppInterface::class, class_implements($entity));
        $this->assertEquals($firstEntity, $entity);
        // TODO Validate the same for company apps.
    }

    public function testListAppIds(): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        /** @var \Apigee\Edge\Api\Management\Controller\AppControllerInterface $controller */
        $controller = $this->getEntityController();
        foreach (array_keys(static::$createdDeveloperApps) as $id) {
            $this->assertContains($id, $controller->listAppIds());
        }
        // TODO Validate the same for company apps.
    }

    public function testListApps(): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        /** @var \Apigee\Edge\Api\Management\Controller\AppControllerInterface $controller */
        $controller = $this->getEntityController();
        $apps = $controller->listApps();
        foreach (static::$createdDeveloperApps as $entity) {
            $this->assertEquals($entity, $apps[$entity->getAppId()]);
        }
        $apps = $controller->listApps(false);
        /** @var \Apigee\Edge\Api\Management\Entity\AppInterface $firstApp */
        $firstApp = reset($apps);
        $this->assertEmpty($firstApp->getCredentials());
        // TODO Validate the same for company apps.
    }

    public function testListAppIdsByStatus(): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        /** @var \Apigee\Edge\Api\Management\Controller\AppControllerInterface $controller */
        $controller = $this->getEntityController();
        $approvedIDs = $controller->listAppIdsByStatus(App::STATUS_APPROVED);
        $revokedIDs = $controller->listAppIdsByStatus(App::STATUS_REVOKED);
        /** @var \Apigee\Edge\Api\Management\Entity\AppInterface $app */
        foreach (static::$createdDeveloperApps as $app) {
            if (App::STATUS_APPROVED === $app->getStatus()) {
                $this->assertContains($app->getAppId(), $approvedIDs);
            } else {
                $this->assertContains($app->getAppId(), $revokedIDs);
            }
        }
    }

    public function testListAppIdsByType(): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        // TODO Implement after company apps are being supported.
        $this->markTestIncomplete('Company apps support is required for complete testing.');
    }

    public function testListAppIdsByFamily(): void
    {
        /*
         * @see https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/appfamilies.
         */
        $this->markTestSkipped(
            'App families API seems to be deprecated.'
        );
    }

    /**
     * @inheritdoc
     */
    protected static function getEntityController(): EntityControllerInterface
    {
        static $controller;
        if (!$controller) {
            $controller = new AppController(
                static::getOrganization(static::$client),
                static::$client
            );
        }

        return $controller;
    }
}
