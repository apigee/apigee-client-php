<?php

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Api\Management\Controller\DeveloperAppController;
use Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialController;
use Apigee\Edge\Api\Management\Entity\AppCredential;
use Apigee\Edge\Api\Management\Entity\AppCredentialInterface;
use Apigee\Edge\Entity\EntityControllerInterface;
use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Structure\CredentialProduct;
use Apigee\Edge\Tests\Test\Controller\EntityControllerValidator;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;
use Apigee\Edge\Tests\Test\Mock\TestClientFactory;

/**
 * Class DeveloperAppCredentialControllerTest.
 *
 * @package Apigee\Edge\Tests\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
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
    protected static function getEntityController(): EntityControllerInterface
    {
        static $controller;
        if (!$controller) {
            $controller = new DeveloperAppCredentialController(
                static::getOrganization(),
                static::$developerId,
                static::$appName,
                static::$client
            );
        }
        return $controller;
    }

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::setupBeforeDeveloperApp();

        $dac = new DeveloperAppController(static::getOrganization(), static::$developerId, static::$client);
        try {
            // We have to keep a copy of phpunit@example.com developer's data because of this for offline tests.
            // See: offline-test-data/v1/organizations/phpunit/developers/phpunit@example.com .
            $entity = $dac->load(DeveloperAppControllerTest::sampleDataForEntityCreate()->id());
            static::$appName = $entity->id();
        } catch (ClientErrorException $e) {
            if ($e->getEdgeErrorCode() && 'developer.service.AppDoesNotExist' === $e->getEdgeErrorCode()) {
                $entity = DeveloperAppControllerTest::sampleDataForEntityCreate();
                $dac->create($entity);
                static::$appName = $entity->id();
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function tearDownAfterClass()
    {
        if (strpos(static::$client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX) === 0) {
            return;
        }

        if (static::$appName === null) {
            return;
        }

        $dac = new DeveloperAppController(static::getOrganization(), static::$developerId, static::$client);
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

    public function testCreatedAppHasAnEmptyCredential()
    {
        $dac = new DeveloperAppController(static::getOrganization(), static::$developerId, static::$client);
        /** @var \Apigee\Edge\Api\Management\Entity\DeveloperAppInterface $entity */
        $entity = $dac->load(static::$appName);
        $credentials = $entity->getCredentials();
        $this->assertCount(1, $credentials);
        /** @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential */
        $credential = reset($credentials);
        $this->assertCount(0, $credential->getApiProducts());
        $this->assertNotEmpty($credential->getConsumerKey());
        $this->assertNotEmpty($credential->getConsumerSecret());
        $this->assertEquals('-1', $credential->getExpiresAt());
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
        $key = static::$appName . '_key';
        $secret = static::$appName . '_secret';
        $credential = $controller->create($key, $secret);
        $this->assertCount(0, $credential->getApiProducts());
        $this->assertEquals($key, $credential->getConsumerKey());
        $this->assertEquals($secret, $credential->getConsumerSecret());
        $this->assertEquals('-1', $credential->getExpiresAt());
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
        $this->assertArraySubset(
            static::$objectNormalizer->normalize($credential),
            static::$objectNormalizer->normalize($loaded)
        );
        return $credential->id();
    }

    /**
     * @depends testLoad
     *
     * @param string $entityId
     */
    public function testAddProducts(string $entityId)
    {
        /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialControllerInterface $controller */
        $controller = $this->getEntityController();
        $credential = $controller->addProducts($entityId, [static::$apiProductName]);
        $productNames = array_map(function ($product) {
            /** @var \Apigee\Edge\Structure\CredentialProduct $product */
            return $product->getApiproduct();
        }, $credential->getApiProducts());
        $this->assertContains(static::$apiProductName, $productNames);
    }

    /**
     * @depends testLoad
     *
     * @param string $entityId
     */
    public function testOverrideAttributes(string $entityId)
    {
        // We can either test this or testApiProductStatusChange() with the offline client, we can not test both
        // because they are using the same path with the same HTTP method.
        if (strpos(static::$client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX) === 0) {
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
    public function testOverrideScopes(string $entityId)
    {
        /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialControllerInterface $controller */
        $controller = $this->getEntityController();
        /** @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential */
        $credential = $controller->load($entityId);
        $this->assertEmpty($credential->getScopes());
        $credential = $controller->overrideScopes($entityId, ['scope 1']);
        $this->assertContains('scope 1', $credential->getScopes());
        if (strpos(static::$client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX) === 0) {
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
    public function testStatusChange(string $entityId)
    {
        if (strpos(static::$client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX) === 0) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialControllerInterface $controller */
        $controller = static::getEntityController();
        /** @var AppCredentialInterface $credential */
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
    public function testApiProductStatusChange(string $entityId)
    {
        if (strpos(static::$client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX) === 0) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialControllerInterface $controller */
        $controller = static::getEntityController();
        /** @var AppCredentialInterface $credential */
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

    public function testGenerate()
    {
        if (strpos(static::$client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX) === 0) {
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
        $productNames = array_map(function ($product) {
            /** @var \Apigee\Edge\Structure\CredentialProduct $product */
            return $product->getApiproduct();
        }, $credential->getApiProducts());
        $this->assertContains(static::$apiProductName, $productNames);
        $this->assertContains('scope 1', $credential->getScopes());
        // Thanks for the offline tests, we can not expect a concrete value here.
        $this->assertNotEquals('-1', $credential->getExpiresAt());
    }
}
