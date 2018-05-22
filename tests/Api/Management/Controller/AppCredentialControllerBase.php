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
use Apigee\Edge\Api\Management\Controller\AppCredentialController;
use Apigee\Edge\Api\Management\Entity\AppCredential;
use Apigee\Edge\Api\Management\Entity\AppCredentialInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Exception\ApiException;
use Apigee\Edge\Structure\CredentialProduct;
use Apigee\Edge\Tests\Test\Controller\AttributesAwareEntityControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerValidator;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;
use Apigee\Edge\Tests\Test\TestClientFactory;

/**
 * Common base test class for \Apigee\Edge\Api\Management\Controller\AppCredentialControllerInterface implementations.
 */
abstract class AppCredentialControllerBase extends EntityControllerValidator
{
    use ApiProductAwareControllerTrait;
    use AttributesAwareEntityControllerTestTrait {
        testAddAttributesToEntity as private traitTestAddAttributesToEntity;
    }
    use OrganizationAwareEntityControllerValidatorTrait;

    /** @var string */
    protected static $appName;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        try {
            parent::setUpBeforeClass();
            static::setUpApiProduct();
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
        static::tearDownApiProduct();
    }

    public function testCreatedAppHasAnEmptyCredential(): void
    {
        /** @var \Apigee\Edge\Api\Management\Controller\AppControllerInterface $controller */
        $controller = static::getAppController();
        /** @var \Apigee\Edge\Api\Management\Entity\DeveloperAppInterface $entity */
        /** @var \Apigee\Edge\Api\Management\Controller\AppByOwnerControllerInterface $controller */
        $entity = $controller->load(static::$appName);
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
        /** @var \Apigee\Edge\Api\Management\Controller\AppCredentialControllerInterface $controller */
        $controller = static::getEntityController();
        // Ensure that generated key always valid. (Random app names used by online tests can contain dot which is not
        // valid according to Apigee Edge.)
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
        /** @var \Apigee\Edge\Api\Management\Controller\AppCredentialControllerInterface $controller */
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
        /** @var \Apigee\Edge\Api\Management\Controller\AppCredentialControllerInterface $controller */
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
    public function testOverrideScopes(string $entityId): void
    {
        /** @var \Apigee\Edge\Api\Management\Controller\AppCredentialControllerInterface $controller */
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
        /** @var \Apigee\Edge\Api\Management\Controller\AppCredentialControllerInterface $controller */
        $controller = static::getEntityController();
        /* @var AppCredentialInterface $credential */
        $controller->setStatus($entityId, AppCredentialController::STATUS_REVOKE);
        $credential = $controller->load($entityId);
        $this->assertEquals($credential->getStatus(), AppCredential::STATUS_REVOKED);
        $controller->setStatus($entityId, AppCredentialController::STATUS_APPROVE);
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
        /** @var \Apigee\Edge\Api\Management\Controller\AppCredentialControllerInterface $controller */
        $controller = static::getEntityController();
        /* @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential */
        $controller->setApiProductStatus(
            $entityId,
            static::$apiProductName,
            AppCredentialController::STATUS_REVOKE
        );
        $credential = $controller->load($entityId);
        /** @var \Apigee\Edge\Structure\CredentialProduct $product */
        foreach ($credential->getApiProducts() as $product) {
            if ($product->getApiproduct() === static::$apiProductName) {
                $this->assertEquals($product->getStatus(), CredentialProduct::STATUS_REVOKED);
                break;
            }
        }
        $controller->setApiProductStatus(
            $entityId,
            static::$apiProductName,
            AppCredentialController::STATUS_APPROVE
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
        /** @var \Apigee\Edge\Api\Management\Controller\AppCredentialControllerInterface $controller */
        $controller = $this->getEntityController();
        /** @var \Apigee\Edge\Api\Management\Entity\AppInterface $app */
        $app = $this->getAppController()->load(static::$appName);
        /** @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential */
        $credential = $controller->generate(
            [static::$apiProductName],
            $app->getAttributes(),
            ['scope 1'],
            604800000
        );

        $productNames = $this->getCredentialProducts($credential);
        $this->assertContains(static::$apiProductName, $productNames);
        $this->assertContains('scope 1', $credential->getScopes());
        // Thanks for the offline tests, we can not expect a concrete value here.
        $this->assertNotEquals('-1', $credential->getExpiresAt());
        /** @var \Apigee\Edge\Api\Management\Entity\AppInterface $updatedApp */
        $updatedApp = $this->getAppController()->load(static::$appName);
        // Credential generation should not deleted any previously existing app credentials.
        $this->assertEquals($app->getAttributes(), $updatedApp->getAttributes());

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
        /** @var \Apigee\Edge\Api\Management\Controller\AppCredentialControllerInterface $controller */
        $controller = static::getEntityController();
        /** @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential */
        $credential = $controller->load($entityId);
        $productNames = $this->getCredentialProducts($credential);
        $this->assertContains(static::$apiProductName, $productNames);
        /* @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential */
        $controller->deleteApiProduct(
            $entityId,
            static::$apiProductName
        );
        $credential = $controller->load($entityId);
        $productNames = $this->getCredentialProducts($credential);
        $this->assertNotContains(static::$apiProductName, $productNames);
    }

    /**
     * We had to override this to change its dependency.
     *
     * @depends testGenerate
     *
     * @param string $entityId
     */
    public function testAddAttributesToEntity(string $entityId): string
    {
        return $this->traitTestAddAttributesToEntity($entityId);
    }

    abstract protected static function getAppController(): AppByOwnerController;

    abstract protected static function getAppSampleDataForEntityCreate(): EntityInterface;

    private function getCredentialProducts(AppCredentialInterface $credential)
    {
        return array_map(function ($product) {
            /* @var \Apigee\Edge\Structure\CredentialProduct $product */
            return $product->getApiproduct();
        }, $credential->getApiProducts());
    }
}
