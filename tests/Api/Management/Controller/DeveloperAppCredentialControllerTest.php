<?php

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Api\Management\Controller\DeveloperAppController;
use Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialController;
use Apigee\Edge\Entity\EntityControllerInterface;
use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\Structure\AttributesProperty;
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
        DeveloperAppControllerTestTrait::setUpBeforeClass as protected setupBeforeDeveloperApp;
        DeveloperAppControllerTestTrait::tearDownAfterClass as protected cleanUpAfterDeveloperApp;
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
                $entity = $dac->create(DeveloperAppControllerTest::sampleDataForEntityCreate());
                static::$appName = $entity->id();
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        static::cleanUpAfterDeveloperApp();

        if (strpos(self::$client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX) === 0) {
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
        return $credential->id();
    }

    /**
     * @depends testCreate
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
     * @depends testCreate
     *
     * @param string $entityId
     */
    public function testOverrideAttributes(string $entityId)
    {
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
     * @depends testCreate
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
        $credential = $controller->overrideScopes($entityId, ['scope 2']);
        $this->assertNotContains('scope 1', $credential->getScopes());
        $this->assertContains('scope 2', $credential->getScopes());
    }

    public function testGenerate()
    {
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
