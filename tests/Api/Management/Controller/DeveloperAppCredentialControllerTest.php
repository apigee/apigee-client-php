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
use Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialController;
use Apigee\Edge\Api\Management\Entity\AppCredential;
use Apigee\Edge\Api\Management\Entity\AppCredentialInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Exception\ApiRequestException;
use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Structure\CredentialProduct;
use Apigee\Edge\Tests\Test\Controller\EntityControllerValidator;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;
use Apigee\Edge\Tests\Test\TestClientFactory;

/**
 * Class DeveloperAppCredentialControllerTest.
 */
class DeveloperAppCredentialControllerTest extends EntityControllerValidator
{
    use DeveloperAppControllerTestTrait {
        setUpBeforeClass as protected setupBeforeDeveloperApp;
        tearDownAfterClass as protected cleanUpAfterDeveloperApp;
    }
    use OrganizationAwareEntityControllerValidatorTrait;

    protected static $appName;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        try {
            parent::setUpBeforeClass();
            static::setupBeforeDeveloperApp();

            $dac = new DeveloperAppController(static::getOrganization(static::$client), static::$developerId, static::$client);
            try {
                // We have to keep a copy of phpunit@example.com developer's data because of this for offline tests.
                // See: offline-test-data/v1/organizations/phpunit/developers/phpunit@example.com .
                $entity = $dac->load(DeveloperAppControllerTest::sampleDataForEntityCreate()->id());
                static::$appName = $entity->id();
            } catch (ClientErrorException $e) {
                if ($e->getEdgeErrorCode() && 'developer.service.AppDoesNotExist' === $e->getEdgeErrorCode()) {
                    $entity = clone DeveloperAppControllerTest::sampleDataForEntityCreate();
                    $dac->create($entity);
                    static::$appName = $entity->id();
                }
            }
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

        if (null === static::$appName) {
            return;
        }

        $dac = new DeveloperAppController(static::getOrganization(static::$client), static::$developerId, static::$client);
        $dacc = new DeveloperAppCredentialController(
            static::getOrganization(static::$client),
            static::$developerId,
            static::$appName,
            static::$client
        );
        // First, we have to delete all credentials of the created test app otherwise we are not going to be able to
        // delete the created test API product because Edge still thinks that there is an app associated with
        // the API product. After that the test API product becomes a ghost and it can not be removed without
        // additional help.
        try {
            /** @var \Apigee\Edge\Api\Management\Entity\DeveloperAppInterface $app */
            $app = $dac->load(static::$appName);
            foreach ($app->getCredentials() as $credential) {
                $dacc->delete($credential->getConsumerKey());
            }
        } catch (ClientErrorException $e) {
            // Hope that we can still remove the created test API product.
        }
        try {
            $dac->delete(static::$appName);
        } catch (ClientErrorException $e) {
            if (!$e->getEdgeErrorCode() || 'developer.service.AppDoesNotExist' !== $e->getEdgeErrorCode()) {
                printf(
                    "Unable to delete developer app entity with %s id.\n",
                    static::$appName
                );
            }
        }

        parent::tearDownAfterClass();
        static::cleanUpAfterDeveloperApp();
    }

    public function testCreatedAppHasAnEmptyCredential(): void
    {
        $dac = new DeveloperAppController(static::getOrganization(static::$client), static::$developerId, static::$client);
        /** @var \Apigee\Edge\Api\Management\Entity\DeveloperAppInterface $entity */
        $entity = $dac->load(static::$appName);
        $credentials = $entity->getCredentials();
        $this->assertCount(1, $credentials);
        /** @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential */
        $credential = reset($credentials);
        $this->assertCount(0, $credential->getApiProducts());
        $this->assertNotEmpty($credential->getConsumerKey());
        $this->assertNotEmpty($credential->getConsumerSecret());
        $this->assertEquals(null, $credential->getExpiresAt());
    }

    /**
     * @depends testCreatedAppHasAnEmptyCredential
     *
     * @return \Apigee\Edge\Api\Management\Entity\AppCredentialInterface
     */
    public function testCreate()
    {
        /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialControllerInterface $controller */
        $controller = $this->getEntityController();
        // Ensure that generated key always valid. (Random app names used by online tests can contain dot which is not
        // valid according to Edge.)
        $key = preg_replace('/[^A-Za-z0-9\-_]/', '', static::$appName . '_key');
        $secret = static::$appName . '_secret';
        $credential = $controller->create($key, $secret);
        $this->assertCount(0, $credential->getApiProducts());
        $this->assertEquals($key, $credential->getConsumerKey());
        $this->assertEquals($secret, $credential->getConsumerSecret());
        $this->assertEquals(null, $credential->getExpiresAt());

        return $credential;
    }

    /**
     * @depends testCreate
     *
     * @param \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential
     *
     * @return string
     */
    public function testLoad(AppCredentialInterface $credential)
    {
        /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialControllerInterface $controller */
        $controller = $this->getEntityController();
        $loaded = $controller->load($credential->id());
        $normalized = static::$objectNormalizer->normalize($loaded);
        $this->assertArraySubset(
            static::$objectNormalizer->normalize($credential),
            $normalized
        );

        return $credential->id();
    }

    /**
     * @depends testLoad
     *
     * @param string $entityId
     */
    public function testAddProducts(string $entityId): void
    {
        /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialControllerInterface $controller */
        $controller = $this->getEntityController();
        $credential = $controller->addProducts($entityId, [static::$apiProductName]);
        $productNames = $this->getCredentialProducts($credential);
        $this->assertContains(static::$apiProductName, $productNames);
    }

    /**
     * @depends testLoad
     *
     * @param string $entityId
     */
    public function testOverrideAttributes(string $entityId): void
    {
        // We can either test this or testApiProductStatusChange() with the offline client, we can not test both
        // because they are using the same path with the same HTTP method.
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialControllerInterface $controller */
        $controller = $this->getEntityController();
        /** @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential */
        $credential = $controller->load($entityId);
        $this->assertEmpty($credential->getAttributes()->values());
        $credential = $controller->overrideAttributes($entityId, new AttributesProperty(['foo' => 'bar']));
        $this->assertEquals('bar', $credential->getAttributeValue('foo'));
        $credential = $controller->overrideAttributes($entityId, new AttributesProperty(['bar' => 'baz']));
        $this->assertArrayNotHasKey('foo', $credential->getAttributes()->values());
        $this->assertEquals('baz', $credential->getAttributeValue('bar'));
    }

    /**
     * @depends testLoad
     *
     * @param string $entityId
     */
    public function testOverrideScopes(string $entityId): void
    {
        /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialControllerInterface $controller */
        $controller = $this->getEntityController();
        /** @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential */
        $credential = $controller->load($entityId);
        $this->assertEmpty($credential->getScopes());
        $credential = $controller->overrideScopes($entityId, ['scope 1']);
        $this->assertContains('scope 1', $credential->getScopes());
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestIncomplete('Test can be completed only with real Apigee Edge connection');
        }
        $credential = $controller->overrideScopes($entityId, ['scope 2']);
        $this->assertNotContains('scope 1', $credential->getScopes());
        $this->assertContains('scope 2', $credential->getScopes());
    }

    /**
     * @depends testLoad
     *
     * @param string $entityId
     */
    public function testStatusChange(string $entityId): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialControllerInterface $controller */
        $controller = static::getEntityController();
        /* @var AppCredentialInterface $credential */
        $controller->setStatus($entityId, DeveloperAppCredentialController::STATUS_REVOKE);
        $credential = $controller->load($entityId);
        $this->assertEquals($credential->getStatus(), AppCredential::STATUS_REVOKED);
        $controller->setStatus($entityId, DeveloperAppCredentialController::STATUS_APPROVE);
        $credential = $controller->load($entityId);
        $this->assertEquals($credential->getStatus(), AppCredential::STATUS_APPROVED);
    }

    /**
     * @depends testLoad
     *
     * @param string $entityId
     */
    public function testApiProductStatusChange(string $entityId): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialControllerInterface $controller */
        $controller = static::getEntityController();
        /* @var AppCredentialInterface $credential */
        $controller->setApiProductStatus(
            $entityId,
            static::$apiProductName,
            DeveloperAppCredentialController::STATUS_REVOKE
        );
        $credential = $controller->load($entityId);
        /** @var CredentialProduct $product */
        foreach ($credential->getApiProducts() as $product) {
            if ($product->getApiproduct() === static::$apiProductName) {
                $this->assertEquals($product->getStatus(), CredentialProduct::STATUS_REVOKED);
                break;
            }
        }
        $controller->setApiProductStatus(
            $entityId,
            static::$apiProductName,
            DeveloperAppCredentialController::STATUS_APPROVE
        );
        $credential = $controller->load($entityId);
        foreach ($credential->getApiProducts() as $product) {
            if ($product->getApiproduct() === static::$apiProductName) {
                $this->assertEquals($product->getStatus(), CredentialProduct::STATUS_APPROVED);
                break;
            }
        }
    }

    /**
     * @return string
     */
    public function testGenerate(): string
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialControllerInterface $controller */
        $controller = $this->getEntityController();
        /** @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential */
        $credential = $controller->generate(
            [static::$apiProductName],
            ['scope 1'],
            604800000
        );
        $productNames = $this->getCredentialProducts($credential);
        $this->assertContains(static::$apiProductName, $productNames);
        $this->assertContains('scope 1', $credential->getScopes());
        // Thanks for the offline tests, we can not expect a concrete value here.
        $this->assertNotEquals('-1', $credential->getExpiresAt());

        return $credential->id();
    }

    /**
     * @depends testGenerate
     */
    public function testDeleteApiProduct(string $entityId): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialControllerInterface $controller */
        $controller = static::getEntityController();
        /** @var AppCredentialInterface $credential */
        $credential = $controller->load($entityId);
        $productNames = $this->getCredentialProducts($credential);
        $this->assertContains(static::$apiProductName, $productNames);
        /* @var AppCredentialInterface $credential */
        $controller->deleteApiProduct(
            $entityId,
            static::$apiProductName
        );
        $credential = $controller->load($entityId);
        $productNames = $this->getCredentialProducts($credential);
        $this->assertNotContains(static::$apiProductName, $productNames);
    }

    /**
     * @inheritdoc
     */
    protected static function getEntityController(): EntityControllerInterface
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

        return $controller;
    }

    private function getCredentialProducts(AppCredentialInterface $credential)
    {
        return array_map(function ($product) {
            /* @var \Apigee\Edge\Structure\CredentialProduct $product */
            return $product->getApiproduct();
        }, $credential->getApiProducts());
    }
}
