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
use Apigee\Edge\Api\Management\Controller\CompanyAppController;
use Apigee\Edge\Api\Management\Controller\DeveloperAppController;
use Apigee\Edge\Api\Management\Entity\App;
use Apigee\Edge\Api\Management\Entity\CompanyAppInterface;
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
    use CompanyAwareControllerTestTrait;
    use OrganizationAwareEntityControllerValidatorTrait;

    /** @var \Apigee\Edge\Api\Management\Entity\AppInterface[] */
    protected static $apps = [];

    /** @var string[] */
    protected static $appIdTypeMap = [];

    /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppControllerInterface */
    protected static $developerAppController;

    /** @var \Apigee\Edge\Api\Management\Controller\CompanyAppControllerInterface */
    protected static $companyAppController;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        $createSampleApps = function (string $appType): void {
            if (!in_array($appType, ['developer', 'company'])) {
                throw new \InvalidArgumentException('App type has to be either company or developer.');
            }

            /* @var \Apigee\Edge\Api\Management\Entity\AppInterface $sampleEntity */
            if ('developer' === $appType) {
                $sampleEntity = clone DeveloperAppControllerTest::sampleDataForEntityCreate();
                $controller = static::$developerAppController;
            } else {
                $sampleEntity = clone CompanyAppControllerTest::sampleDataForEntityCreate();
                $controller = static::$companyAppController;
            }
            $idField = $sampleEntity->idProperty();
            /** @var \Apigee\Edge\Api\Management\Entity\AppInterface[] $testApps */
            $testApps = [$sampleEntity];
            for ($i = 1; $i <= 5; ++$i) {
                $testApps[$i] = clone $sampleEntity;
                $testApps[$i]->{'set' . $idField}($i . $sampleEntity->id());
            }
            // Create test data on the server or do not do anything if an offline client is in use.
            if (!TestClientFactory::isMockClient(static::$client)) {
                $i = 0;
                foreach ($testApps as $item) {
                    /* @var \Apigee\Edge\Api\Management\Entity\AppInterface $item */
                    /* @var \Apigee\Edge\Api\Management\Entity\AppInterface $tmp */
                    $controller->create($item);
                    if ($i % 2) {
                        $controller->setStatus($item->id(),
                            AppController::STATUS_REVOKE);
                        // Get the updated entity from Edge.
                        $item = $controller->load($item->id());
                    }
                    static::$appIdTypeMap[$item->getAppId()] = $appType;
                    static::$apps["{$appType}_{$item->getAppId()}"] = $item;
                    // Ensure that testLoadApp() works in the online test.
                    static::$apps["{$appType}_{$item->id()}"] = $item;
                    ++$i;
                }
            } else {
                // Ensure that testLoadApp() works in the offline test.
                static::$apps["{$appType}_{$sampleEntity->id()}"] = $controller->load(
                    $sampleEntity->id()
                );
            }
        };

        try {
            parent::setUpBeforeClass();
            static::setupDeveloper();
            static::setupCompany();

            /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppController $developerAppController */
            static::$developerAppController = new DeveloperAppController(
                static::getOrganization(static::$client),
                static::$developerId,
                static::$client
            );
            /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppController $developerAppController */
            static::$companyAppController = new CompanyAppController(
                static::getOrganization(static::$client),
                static::$companyName,
                static::$client
            );

            $createSampleApps('developer');
            $createSampleApps('company');
        } catch (ApiRequestException $e) {
            // Ensure that created test data always gets removed after an API call fails here.
            // (By default tearDownAfterClass() is not called if (any) exception occurred here.)
            static::tearDownAfterClass();
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

        // Remove created apps on Apigee Edge.
        try {
            foreach (static::$appIdTypeMap as $appId => $type) {
                $entity = static::$apps["{$type}_{$appId}"];
                if ($entity instanceof DeveloperAppInterface) {
                    static::$developerAppController->delete($entity->id());
                } else {
                    static::$companyAppController->delete($entity->id());
                }
                unset(static::$appIdTypeMap[$appId]);
                unset(static::$apps["{$type}_{$entity->getAppId()}"]);
                unset(static::$apps["{$type}_{$entity->id()}"]);
            }
        } catch (\Exception $e) {
            printf("Unable to delete %s entity with %s id.\n", strtolower(get_class($entity)), $entity->id());
        }

        parent::tearDownAfterClass();
        static::tearDownDeveloper();
        static::tearDownCompany();
    }

    public function testLoadApp(): void
    {
        /** @var \Apigee\Edge\Api\Management\Controller\AppControllerInterface $controller */
        $controller = $this->getEntityController();
        $sampleEntity = clone DeveloperAppControllerTest::sampleDataForEntityCreate();
        $firstEntity = static::$apps["developer_{$sampleEntity->id()}"];
        $entity = $controller->loadApp($firstEntity->getAppId());
        $this->assertContains(DeveloperAppInterface::class, class_implements($entity));
        $this->assertEquals($firstEntity, $entity);
        $sampleEntity = clone CompanyAppControllerTest::sampleDataForEntityCreate();
        $firstEntity = static::$apps["company_{$sampleEntity->id()}"];
        $entity = $controller->loadApp($firstEntity->getAppId());
        $this->assertContains(CompanyAppInterface::class, class_implements($entity));
        $this->assertEquals($firstEntity, $entity);
    }

    public function testListAppIds(): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        /** @var \Apigee\Edge\Api\Management\Controller\AppControllerInterface $controller */
        $controller = $this->getEntityController();
        $ids = $controller->listAppIds();
        foreach (array_keys(static::$appIdTypeMap) as $id) {
            $this->assertContains($id, $ids);
        }
    }

    public function testListApps(): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        /** @var \Apigee\Edge\Api\Management\Controller\AppControllerInterface $controller */
        $controller = $this->getEntityController();
        $apps = $controller->listApps();
        foreach (static::$appIdTypeMap as $appId => $type) {
            $this->assertEquals(static::$apps["{$type}_{$appId}"], $apps[$appId]);
        }
        $apps = $controller->listApps(false);
        /** @var \Apigee\Edge\Api\Management\Entity\AppInterface $firstApp */
        $firstApp = reset($apps);
        $this->assertEmpty($firstApp->getCredentials());
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
        /* @var \Apigee\Edge\Api\Management\Entity\AppInterface $app */
        foreach (static::$appIdTypeMap as $appId => $type) {
            $app = static::$apps["{$type}_{$appId}"];
            if (App::STATUS_APPROVED === $app->getStatus()) {
                $this->assertContains($appId, $approvedIDs);
            } else {
                $this->assertContains($appId, $revokedIDs);
            }
        }
    }

    public function testListAppIdsByType(): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }

        /** @var \Apigee\Edge\Api\Management\Controller\AppControllerInterface $controller */
        $controller = $this->getEntityController();
        $developerApps = $controller->listAppIdsByType('developer');
        $companyApps = $controller->listAppIdsByType('company');
        /* @var \Apigee\Edge\Api\Management\Entity\AppInterface $app */
        foreach (static::$appIdTypeMap as $appId => $type) {
            $app = static::$apps["{$type}_{$appId}"];
            if ($app instanceof DeveloperAppInterface) {
                $this->assertContains($app->getAppId(), $developerApps);
            } else {
                $this->assertContains($app->getAppId(), $companyApps);
            }
        }
    }

    public function testListAppIdsByFamily(): void
    {
        /*
         * @see https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/appfamilies.
         */
        $this->markTestSkipped('App families API seems to be deprecated.');
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
