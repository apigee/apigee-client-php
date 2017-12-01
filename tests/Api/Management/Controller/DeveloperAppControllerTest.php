<?php

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Api\Management\Controller\ApiProductController;
use Apigee\Edge\Api\Management\Controller\DeveloperAppController;
use Apigee\Edge\Api\Management\Controller\DeveloperController;
use Apigee\Edge\Api\Management\Entity\DeveloperApp;
use Apigee\Edge\Entity\EntityCrudOperationsInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Entity\EntityNormalizer;
use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Tests\Test\Controller\AttributesAwareEntityControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\NonCpsLimitEntityControllerValidator;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;
use Apigee\Edge\Tests\Test\Mock\TestClientFactory;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class DeveloperAppControllerTest.
 *
 * @package Apigee\Edge\Tests\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @group controller
 */
class DeveloperAppControllerTest extends NonCpsLimitEntityControllerValidator
{
    use AttributesAwareEntityControllerTestTrait;
    use OrganizationAwareEntityControllerValidatorTrait;

    protected static $developerId;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::$objectNormalizer = new ObjectNormalizer();
        static::$objectNormalizer->setSerializer(new Serializer([new EntityNormalizer()]));
        // Create required entities for testing this controller on Edge.
        // Unfortunately in PHPUnit it is not possible to directly depend on another test classes. This could ensure
        // that even if only a single test case is executed from this class those dependencies are executed first and
        // they could create these entities on Edge.
        $dc = new DeveloperController(static::getOrganization(), static::$client);
        try {
            // We have to keep a copy of phpunit@example.com developer's data because of this for offline tests.
            // See: offline-test-data/v1/organizations/phpunit/developers/phpunit@example.com .
            $entity = $dc->load(DeveloperControllerTest::sampleDataForEntityCreate()->getEmail());
            static::$developerId = $entity->id();
        } catch (ClientErrorException $e) {
            if ($e->getEdgeErrorCode() && 'developer.service.DeveloperDoesNotExist' === $e->getEdgeErrorCode()) {
                $entity = $dc->create(DeveloperControllerTest::sampleDataForEntityCreate());
                static::$developerId = $entity->id();
            }
        }

        $apc = new ApiProductController(static::getOrganization(), static::$client);
        try {
            $apc->load(ApiProductControllerTest::sampleDataForEntityCreate()->id());
        } catch (ClientErrorException $e) {
            if ($e->getEdgeErrorCode() && 'keymanagement.service.apiproduct_doesnot_exist' === $e->getEdgeErrorCode()) {
                $apc->create(ApiProductControllerTest::sampleDataForEntityCreate());
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        if (strpos(self::$client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX) === 0) {
            return;
        }

        $dc = new DeveloperController(static::getOrganization(), static::$client);
        try {
            $dc->delete(DeveloperControllerTest::sampleDataForEntityCreate()->getEmail());
        } catch (\Exception $e) {
            printf(
                "Unable to delete developer entity with %s id.\n",
                DeveloperControllerTest::sampleDataForEntityCreate()->getEmail()
            );
        }

        $apc = new ApiProductController(static::getOrganization(), static::$client);
        try {
            $apc->delete(ApiProductControllerTest::sampleDataForEntityCreate()->id());
        } catch (\Exception $e) {
            printf(
                "Unable to delete api product entity with %s id.\n",
                ApiProductControllerTest::sampleDataForEntityCreate()->id()
            );
        }
    }

    /**
     * @inheritdoc
     */
    protected static function getEntityController(): EntityCrudOperationsInterface
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
    public static function sampleDataForEntityCreate(): EntityInterface
    {
        $entity = new DeveloperApp([
            'name' => 'phpunit_test_app_1',
            'apiProducts' => [ApiProductControllerTest::sampleDataForEntityCreate()->id()],
            'attributes' => new AttributesProperty(['foo' => 'bar']),
            'callbackUrl' => 'http://example.com',
        ]);
        $entity->setDisplayName('PHP Unit: Test app 1');
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
        $entity->setDisplayName('(Edited) PHP Unit: Test app 1');
        return $entity;
    }

    protected static function expectedAfterEntityCreate(): EntityInterface
    {
        /** @var DeveloperApp $entity */
        $entity = parent::expectedAfterEntityCreate();
        $entity->setStatus('approved');
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
}
