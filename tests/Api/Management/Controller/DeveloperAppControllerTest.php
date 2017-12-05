<?php

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Api\Management\Controller\DeveloperAppController;
use Apigee\Edge\Api\Management\Entity\DeveloperApp;
use Apigee\Edge\Entity\EntityControllerInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Tests\Test\Controller\AttributesAwareEntityControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\NonCpsLimitEntityControllerValidator;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;

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
    use DeveloperAppControllerTestTrait {
        DeveloperAppControllerTestTrait::setUpBeforeClass as protected setupBeforeDeveloperApp;
        DeveloperAppControllerTestTrait::tearDownAfterClass as protected cleanUpAfterDeveloperApp;
    }
    use OrganizationAwareEntityControllerValidatorTrait;

    protected static $developerId;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::setupBeforeDeveloperApp();
    }

    /**
     * @inheritdoc
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        static::cleanUpAfterDeveloperApp();
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

    /**
     * @inheritdoc
     */
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
