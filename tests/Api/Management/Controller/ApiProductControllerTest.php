<?php

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Api\Management\Controller\ApiProductController;
use Apigee\Edge\Api\Management\Entity\ApiProduct;
use Apigee\Edge\Entity\EntityControllerInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Tests\Test\Controller\AttributesAwareEntityControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\NonCpsLimitEntityControllerValidator;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;

/**
 * Class ApiProductControllerTest.
 *
 * @package Apigee\Edge\Tests\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @group controller
 */
class ApiProductControllerTest extends NonCpsLimitEntityControllerValidator
{
    use AttributesAwareEntityControllerTestTrait;
    use OrganizationAwareEntityControllerValidatorTrait;

    /**
     * @inheritdoc
     */
    protected static function getEntityController(): EntityControllerInterface
    {
        static $controller;
        if (!$controller) {
            $controller = new ApiProductController(static::getOrganization(), static::$client);
        }
        return $controller;
    }

    /**
     * @inheritdoc
     */
    public static function sampleDataForEntityCreate(): EntityInterface
    {
        return new ApiProduct([
            'name' => 'phpunit_test',
            'displayName' => 'PHP Unit Test product',
            'approvalType' => ApiProduct::APPROVAL_TYPE_AUTO,
            'attributes' => new AttributesProperty(['foo' => 'bar']),
            'scopes' => ['scope 1', 'scope 2']
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function sampleDataForEntityUpdate(): EntityInterface
    {
        return new ApiProduct([
            'displayName' => '(Edited) PHP Unit Test product',
            'approvalType' => ApiProduct::APPROVAL_TYPE_MANUAL,
            'attributes' => new AttributesProperty(['foo' => 'foo', 'bar' => 'baz']),
        ]);
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
     * @depends testCreate
     */
    public function testSearchByAttribute()
    {
        /** @var ApiProductController $controller */
        $controller = $this->getEntityController();
        /** @var \Apigee\Edge\Entity\Property\AttributesPropertyAwareTrait $entity */
        $entity = self::sampleDataForEntityCreate();
        $unexpected = 'should_not_appear';
        $entity->setName($unexpected);
        $entity->setAttribute('foo', 'foo');
        $entity = $controller->create($entity);
        self::$createdEntities[$entity->id()] = $entity;
        $ids = $controller->searchByAttribute('foo', 'bar');
        $this->assertContains('phpunit_test', $ids);
        $this->assertNotContains($unexpected, $ids);
    }
}
