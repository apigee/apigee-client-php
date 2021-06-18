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

use Apigee\Edge\Api\Management\Controller\AppCredentialControllerInterface;
use Apigee\Edge\Api\Management\Entity\AppCredentialInterface;
use Apigee\Edge\Api\Management\Entity\AppInterface;
use Apigee\Edge\Api\Management\Entity\AppOwnerInterface;
use Apigee\Edge\Structure\CredentialProductInterface;
use Apigee\Edge\Tests\Api\Management\Entity\ApiProductTestEntityProviderTrait;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\DefaultTestOrganizationAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\TestClientFactory;
use Apigee\Edge\Tests\Test\Utility\MarkOnlineTestSkippedAwareTrait;

/**
 * Base class for developer- and company app credential tests.
 */
abstract class AppCredentialControllerTestBase extends EntityControllerTestBase
{
    use ApiProductControllerAwareTestTrait;
    use ApiProductTestEntityProviderTrait;
    use DefaultAPIClientAwareTrait;
    use DefaultTestOrganizationAwareTrait;
    use MarkOnlineTestSkippedAwareTrait;
    // The order of these trait matters. Check @depends in test methods.
    use AttributesAwareEntityControllerTestTrait;

    /** @var \Apigee\Edge\Api\Management\Entity\ApiProductInterface */
    protected static $testApiProduct;

    /** @var \Apigee\Edge\Api\Management\Entity\AppOwnerInterface */
    protected static $testAppOwner;

    /** @var \Apigee\Edge\Api\Management\Entity\AppInterface */
    protected static $testApp;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$testApiProduct = static::getNewApiProduct();
        static::apiProductController(static::defaultAPIClient())->create(static::$testApiProduct);
        static::$testAppOwner = static::setupTestAppOwner();
        static::$testApp = static::setupTestApp(static::setupTestAppOwner());
    }

    public function testCreatedAppHasAnEmptyCredential(): void
    {
        /** @var \Apigee\Edge\Api\Management\Entity\DeveloperAppInterface $entity */
        /** @var \Apigee\Edge\Api\Management\Controller\AppByOwnerControllerInterface $controller */
        $entity = static::appByOwnerController()->load(static::$testApp->id());
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
    public function testCreate(): AppCredentialInterface
    {
        // Ensure that generated key always valid. (Random app names used by online tests can contain dot which is not
        // valid according to Apigee Edge.)
        $key = preg_replace('/[^A-Za-z0-9\-_]/', '', static::$testApp->getName() . '_key');
        $secret = static::$testApp->getName() . '_secret';
        $credential = static::entityController()->create($key, $secret);
        $this->assertCount(0, $credential->getApiProducts());
        $this->assertEquals($key, $credential->getConsumerKey());
        $this->assertEquals($secret, $credential->getConsumerSecret());
        $this->assertEquals(null, $credential->getExpiresAt());

        return $credential;
    }

    /**
     * @depends testCreate
     *
     * @param \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $created
     *
     * @return string
     */
    public function testLoad(AppCredentialInterface $created)
    {
        /** @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $loaded */
        $loaded = static::entityController()->load($created->id());
        $this->assertCount(count($loaded->getApiProducts()), $created->getApiProducts());
        $this->assertEquals($created->getConsumerKey(), $loaded->getConsumerKey());
        $this->assertEquals($created->getConsumerSecret(), $loaded->getConsumerSecret());
        $this->assertEquals($created->getIssuedAt()->getTimestamp(), $loaded->getIssuedAt()->getTimestamp());
        $this->assertEquals($created->getExpiresAt(), $loaded->getExpiresAt());

        return $loaded->id();
    }

    /**
     * @depends testLoad
     *
     * @param string $entityId
     */
    public function testAddProducts(string $entityId): void
    {
        /** @var \Apigee\Edge\Api\Management\Controller\AppCredentialControllerInterface $controller */
        $controller = $this->entityController();
        $credential = $controller->addProducts($entityId, [static::$testApiProduct->id()]);
        $productNames = $this->getCredentialProducts($credential);
        $this->assertContains(static::$testApiProduct->id(), $productNames);
    }

    /**
     * @depends testLoad
     *
     * @param string $entityId
     */
    public function testOverrideScopes(string $entityId): void
    {
        /** @var \Apigee\Edge\Api\Management\Controller\AppCredentialControllerInterface $controller */
        $controller = $this->entityController();
        /** @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential */
        $credential = $controller->load($entityId);
        $this->assertEmpty($credential->getScopes());
        $credential = $controller->overrideScopes($entityId, ['scope 1']);
        $this->assertContains('scope 1', $credential->getScopes());
        if (TestClientFactory::isOfflineClient(static::defaultAPIClient())) {
            $this->markTestIncomplete(__FUNCTION__ . ' can be completed only with an online API test client.');
        }
        $credential = $controller->overrideScopes($entityId, ['scope 2']);
        $this->assertNotContains('scope 1', $credential->getScopes());
        $this->assertContains('scope 2', $credential->getScopes());
    }

    /**
     * @depends testLoad
     *
     * @group online
     *
     * @param string $entityId
     */
    public function testStatusChange(string $entityId): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        /** @var \Apigee\Edge\Api\Management\Controller\AppCredentialControllerInterface $controller */
        $controller = static::entityController();
        /* @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential */
        $controller->setStatus($entityId, AppCredentialControllerInterface::STATUS_REVOKE);
        $credential = $controller->load($entityId);
        $this->assertEquals($credential->getStatus(), AppCredentialInterface::STATUS_REVOKED);
        $controller->setStatus($entityId, AppCredentialControllerInterface::STATUS_APPROVE);
        $credential = $controller->load($entityId);
        $this->assertEquals($credential->getStatus(), AppCredentialInterface::STATUS_APPROVED);
    }

    /**
     * @depends testLoad
     *
     * @group online
     *
     * @param string $entityId
     */
    public function testApiProductStatusChange(string $entityId): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        /** @var \Apigee\Edge\Api\Management\Controller\AppCredentialControllerInterface $controller */
        $controller = static::entityController();
        /* @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential */
        $controller->setApiProductStatus(
            $entityId,
            static::$testApiProduct->id(),
            AppCredentialControllerInterface::STATUS_REVOKE
        );
        $credential = $controller->load($entityId);
        /** @var \Apigee\Edge\Structure\CredentialProduct $product */
        foreach ($credential->getApiProducts() as $product) {
            if ($product->getApiproduct() === static::$testApiProduct->id()) {
                $this->assertEquals($product->getStatus(), CredentialProductInterface::STATUS_REVOKED);
                break;
            }
        }
        $controller->setApiProductStatus(
            $entityId,
            static::$testApiProduct->id(),
            AppCredentialControllerInterface::STATUS_APPROVE
        );
        $credential = $controller->load($entityId);
        foreach ($credential->getApiProducts() as $product) {
            if ($product->getApiproduct() === static::$testApiProduct->id()) {
                $this->assertEquals($product->getStatus(), CredentialProductInterface::STATUS_APPROVED);
                break;
            }
        }
    }

    /**
     * @group online
     *
     * @return string
     */
    public function testGenerate(): string
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        /** @var \Apigee\Edge\Api\Management\Controller\AppCredentialControllerInterface $controller */
        $controller = $this->entityController();
        /** @var \Apigee\Edge\Api\Management\Entity\AppInterface $app */
        $app = static::appByOwnerController()->load(static::$testApp->id());
        /** @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential */
        $credential = $controller->generate(
            [static::$testApiProduct->id()],
            $app->getAttributes(),
            $app->getCallbackUrl(),
            ['scope 1'],
            604800000
        );

        $productNames = $this->getCredentialProducts($credential);
        $this->assertContains(static::$testApiProduct->id(), $productNames);
        $this->assertContains('scope 1', $credential->getScopes());
        // Thanks for the offline tests, we can not expect a concrete value
        // here.
        $this->assertNotEquals('-1', $credential->getExpiresAt());
        /** @var \Apigee\Edge\Api\Management\Entity\AppInterface $updatedApp */
        $updatedApp = static::appByOwnerController()->load(static::$testApp->id());
        // Credential generation should not deleted any previously existing app
        //credentials.
        $this->assertEquals($app->getAttributes(), $updatedApp->getAttributes());

        return $credential->id();
    }

    /**
     * @depends testGenerate
     *
     * @group online
     */
    public function testDeleteApiProduct(string $entityId): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        /** @var \Apigee\Edge\Api\Management\Controller\AppCredentialControllerInterface $controller */
        $controller = static::entityController();
        /** @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential */
        $credential = $controller->load($entityId);
        $productNames = $this->getCredentialProducts($credential);
        $this->assertContains(static::$testApiProduct->id(), $productNames);
        /* @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential */
        $controller->deleteApiProduct(
            $entityId,
            static::$testApiProduct->id()
        );
        $credential = $controller->load($entityId);
        $productNames = $this->getCredentialProducts($credential);
        $this->assertNotContains(static::$testApiProduct->id(), $productNames);
    }

    /**
     * @depends testGenerate
     */
    public function testAddAttributesToEntity(): string
    {
        /** @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential */
        $credentials = static::$testApp->getCredentials();
        $credential = reset($credentials);
        /** @var \Apigee\Edge\Structure\AttributesProperty $attributes */
        $attributes = $credential->getAttributes();
        $originalAttributes = $attributes->values();
        $attributes->add('name1', 'value1');
        $attributes->add('name2', 'value2');
        /** @var \Apigee\Edge\Structure\AttributesProperty $attributesProperty */
        $attributesProperty = static::entityController()->updateAttributes($credential->id(), $attributes);
        /** @var array $newAttributes */
        $newAttributes = $attributesProperty->values();
        $this->assertArraySubset($originalAttributes, $newAttributes);
        $this->assertArrayHasKey('name1', $newAttributes);
        $this->assertArrayHasKey('name2', $newAttributes);
        $this->assertEquals('value1', $newAttributes['name1']);
        $this->assertEquals('value2', $newAttributes['name2']);

        return $credential->id();
    }

    /**
     * @depends testCreatedAppHasAnEmptyCredential
     *
     * @group online
     */
    public function testDelete(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $newCredential = static::entityController()->generate(
            [static::$testApiProduct->id()],
            static::$testApp->getAttributes(),
            static::$testApp->getCallbackUrl(),
            ['scope 1'],
            604800000
        );
        static::$testApp = static::appByOwnerController()->load(static::$testApp->id());
        /** @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $loaded */
        static::entityController()->delete($newCredential->id());
        static::$testApp = static::appByOwnerController()->load(static::$testApp->id());
        $found = false;
        foreach (static::$testApp->getCredentials() as $cred) {
            if ($newCredential->id() === $cred->id()) {
                $found = true;
            }
        }
        $this->assertFalse($found, 'Credential credential has not been deleted.');
    }

    abstract protected static function setupTestApp(AppOwnerInterface $appOwner): AppInterface;

    abstract protected static function setupTestAppOwner(): AppOwnerInterface;

    /**
     * @return \Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface|\Apigee\Edge\Api\Management\Controller\AppByOwnerControllerInterface
     */
    abstract protected static function appByOwnerController(): EntityControllerTesterInterface;

    private function getCredentialProducts(AppCredentialInterface $credential)
    {
        return array_map(function ($product) {
            /* @var \Apigee\Edge\Structure\CredentialProduct $product */
            return $product->getApiproduct();
        }, $credential->getApiProducts());
    }
}
