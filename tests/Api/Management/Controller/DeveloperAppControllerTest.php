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

use Apigee\Edge\Api\Management\Controller\DeveloperAppController;
use Apigee\Edge\Api\Management\Controller\DeveloperController;
use Apigee\Edge\Api\Management\Entity\DeveloperApp;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Exception\ApiException;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Tests\Test\Controller\AttributesAwareEntityControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;
use Apigee\Edge\Tests\Test\HttpClient\FileSystemHttpMockHttpClient;
use Apigee\Edge\Tests\Test\TestClientFactory;

/**
 * Class DeveloperAppControllerTest.
 *
 * @group controller
 */
class DeveloperAppControllerTest extends AppByOwnerControllerBase
{
    use AttributesAwareEntityControllerTestTrait;
    use DeveloperAwareControllerTestTrait;
    use OrganizationAwareEntityControllerValidatorTrait;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        try {
            parent::setUpBeforeClass();
            static::setupDeveloper();
        } catch (ApiException $e) {
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
        parent::tearDownAfterClass();
        static::tearDownDeveloper();
    }

    /**
     * @inheritdoc
     */
    public static function sampleDataForEntityCreate(): EntityInterface
    {
        static $entity;
        if (null === $entity) {
            $isMock = TestClientFactory::isMockClient(static::$client);
            $entity = new DeveloperApp(
                [
                    'name' => $isMock ? static::getOfflineEntityId() : static::$random->unique()->userName,
                    'apiProducts' => [static::$apiProductName],
                    'attributes' => new AttributesProperty(['foo' => 'bar']),
                    'callbackUrl' => 'http://example.com',
                ]
            );
            $entity->setDisplayName(
                $isMock ? 'PHP Unit: Test app' : static::$random->unique()->words(
                    static::$random->numberBetween(1, 8),
                    true
                )
            );
            $entity->setDescription($isMock ? 'This is a test app created by PHP Unit.' : static::$random->text());
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public static function sampleDataForEntityUpdate(): EntityInterface
    {
        static $entity;
        if (null === $entity) {
            $isMock = TestClientFactory::isMockClient(static::$client);
            $entity = new DeveloperApp(
                [
                    'attributes' => new AttributesProperty(['foo' => 'foo', 'bar' => 'baz']),
                    'callbackUrl' => $isMock ? 'http://foo.example.com' : static::$random->url,
                ]
            );
            $entity->setDisplayName(
                $isMock ? '(Edited) PHP Unit: Test app' : static::$random->unique()->words(
                    static::$random->numberBetween(1, 8),
                    true
                )
            );
            $entity->setDescription(
                $isMock ? '(Edited) This is a test app created by PHP Unit.' : static::$random->unique()->text()
            );
        }

        return $entity;
    }

    /**
     * We have to override this otherwise dependents of this function are being skipped.
     * Also, "@inheritdoc" is not going to work in case of "@depends" annotations so those must be repeated.
     *
     * @inheritdoc
     */
    public function testCreate()
    {
        return parent::testCreate();
    }

    /**
     * It is easier to test it here instead in the DeveloperControllerTest.
     */
    public function testDeveloperHasApp(): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        $controller = new DeveloperController(
            static::getOrganization(static::$client),
            static::$client
        );
        $entity = clone static::sampleDataForEntityCreate();
        $entity->{'set' . ucfirst($entity->idProperty())}($entity->id() . '_has');
        $this->getEntityController()->create($entity);
        static::$createdEntities[$entity->id()] = $entity;
        /** @var \Apigee\Edge\Api\Management\Entity\DeveloperInterface $developer */
        $developer = $controller->load(static::$developerId);
        $this->assertTrue($developer->hasApp($entity->id()));
        $this->getEntityController()->delete($entity->id());
        $developer = $controller->load(static::$developerId);
        $this->assertFalse($developer->hasApp($entity->id()));
        unset(static::$createdEntities[$entity->id()]);
    }

    /**
     * @inheritdoc
     */
    public function paginatedTestEntityIdProvider(): array
    {
        return [['name']];
    }

    /**
     * @inheritdoc
     */
    public static function getOfflineEntityId(): string
    {
        return 'phpunit_test_app';
    }

    /**
     * @inheritdoc
     */
    protected static function getEntityController(ClientInterface $client = null): EntityControllerInterface
    {
        static $controller;
        if (null === $client) {
            if (null === $controller) {
                $controller = new DeveloperAppController(
                    static::getOrganization(static::$client),
                    static::$developerId,
                    static::$client
                );
            }

            return $controller;
        }

        return new DeveloperAppController(static::getOrganization($client), static::$developerId, $client);
    }

    /**
     * @inheritdoc
     */
    protected static function expectedAfterEntityCreate(): EntityInterface
    {
        /** @var \Apigee\Edge\Api\Management\Entity\DeveloperApp $entity */
        $entity = parent::expectedAfterEntityCreate();
        $entity->setStatus('approved');
        // The testCreate test would fail without this because ObjectNormalizer creates displayName and description
        // properties on entities (because of the existence of getters) because these are not in the
        // Edge response, at least not as entity properties.
        $entity->deleteAttribute('DisplayName');
        $entity->deleteAttribute('Notes');

        return $entity;
    }

    protected static function expectedAfterEntityUpdate(): EntityInterface
    {
        /** @var \Apigee\Edge\Api\Management\Entity\DeveloperApp $entity */
        $entity = parent::expectedAfterEntityUpdate();
        // The testUpdate test would fail without this because ObjectNormalizer creates displayName and description
        // properties on entities (because of the existence of getters) but these are not in the
        // Edge response, at least not as entity properties.
        $entity->deleteAttribute('DisplayName');
        $entity->deleteAttribute('Notes');

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityControllerWithMockClient(): EntityControllerInterface
    {
        $client = TestClientFactory::getClient(FileSystemHttpMockHttpClient::class);

        return new DeveloperAppController(static::getOrganization($client), 'f43ffa3c-e147-47de-8cd6-f5b34429a531', $client);
    }
}
