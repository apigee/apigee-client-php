<?php

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Api\Management\Controller\ApiProductController;
use Apigee\Edge\Api\Management\Entity\ApiProduct;
use Apigee\Edge\Entity\BaseEntityControllerInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Tests\Test\Controller\AttributesAwareEntityControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerValidator;

/**
 * Class ApiProductControllerTest.
 *
 * @package Apigee\Edge\Tests\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class ApiProductControllerTest extends EntityControllerValidator
{
    use AttributesAwareEntityControllerTestTrait;

    /**
     * @inheritdoc
     */
    protected static function getEntityController(): BaseEntityControllerInterface
    {
        static $controller;
        if (!$controller) {
            $controller = new ApiProductController(static::$organization, static::$client);
        }
        return $controller;
    }

    /**
     * @inheritdoc
     */
    protected function sampleDataForEntityCreate(): EntityInterface
    {
        return new ApiProduct([
            'name' => 'phpunit_test',
            'displayName' => 'PHP Unit Test product',
            'approvalType' => ApiProduct::APPROVAL_TYPE_AUTO,
            'attributes' => new AttributesProperty(['foo' => 'bar']),
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function sampleDataForEntityUpdate(): EntityInterface
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
        $entity = $this->sampleDataForEntityCreate();
        $unexpected = 'should_not_appear';
        $entity->setName($unexpected);
        $entity->addAttribute('foo', 'foo');
        $entity = $controller->create($entity);
        self::$createdEntities[$entity->id()] = $entity;
        $ids = $controller->searchByAttribute('foo', 'bar');
        $this->assertContains('phpunit_test', $ids);
        $this->assertNotContains($unexpected, $ids);
    }
}
