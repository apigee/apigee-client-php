<?php

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Api\Management\Controller\DeveloperAppController;
use Apigee\Edge\Api\Management\Controller\DeveloperController;
use Apigee\Edge\Api\Management\Entity\DeveloperApp;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Tests\Test\Controller\AttributesAwareEntityControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\NonCpsLimitEntityControllerValidator;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;
use Apigee\Edge\Tests\Test\Mock\TestClientFactory;

/**
 * Class DeveloperAppControllerTest.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @group controller
 */
class DeveloperAppControllerTest extends NonCpsLimitEntityControllerValidator
{
    use AttributesAwareEntityControllerTestTrait;
    use DeveloperAppControllerTestTrait {
        setUpBeforeClass as protected setupBeforeDeveloperApp;
        tearDownAfterClass as protected cleanUpAfterDeveloperApp;
    }
    use OrganizationAwareEntityControllerValidatorTrait;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::setupBeforeDeveloperApp();
    }

    /**
     * @inheritdoc
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::cleanUpAfterDeveloperApp();
    }

    /**
     * @inheritdoc
     */
    public static function sampleDataForEntityCreate(): EntityInterface
    {
        $entity = new DeveloperApp([
            'name' => 'phpunit_test_app',
            'apiProducts' => [ApiProductControllerTest::sampleDataForEntityCreate()->id()],
            'attributes' => new AttributesProperty(['foo' => 'bar']),
            'callbackUrl' => 'http://example.com',
        ]);
        $entity->setDisplayName('PHP Unit: Test app');
        $entity->setDescription('This is a test app created by PHP Unit.');

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public static function sampleDataForEntityUpdate(): EntityInterface
    {
        $entity = new DeveloperApp([
            'attributes' => new AttributesProperty(['foo' => 'foo', 'bar' => 'baz']),
            'callbackUrl' => 'http://foo.example.com',
        ]);
        $entity->setDisplayName('(Edited) PHP Unit: Test app');
        $entity->setDescription('(Edited) This is a test app created by PHP Unit.');

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
     * It is easier to test it here instead in the DeveloperAppControllerTest.
     */
    public function testDeveloperHasApp(): void
    {
        if (0 === strpos(static::$client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        $controller = new DeveloperController(
            static::getOrganization(),
            static::$client
        );
        $entity = static::sampleDataForEntityCreate();
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
    protected static function getEntityController(): EntityControllerInterface
    {
        static $controller;
        if (!$controller) {
            $controller = new DeveloperAppController(
                static::getOrganization(),
                static::$developerId,
                static::$client
            );
        }

        return $controller;
    }

    /**
     * @inheritdoc
     */
    protected static function expectedAfterEntityCreate(): EntityInterface
    {
        /** @var DeveloperApp $entity */
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
        /** @var DeveloperApp $entity */
        $entity = parent::expectedAfterEntityUpdate();
        // The testUpdate test would fail without this because ObjectNormalizer creates displayName and description
        // properties on entities (because of the existence of getters) because these are not in the
        // Edge response, at least not as entity properties.
        $entity->deleteAttribute('DisplayName');
        $entity->deleteAttribute('Notes');

        return $entity;
    }
}
