<?php

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Api\Management\Controller\ApiProductController;
use Apigee\Edge\Api\Management\Entity\ApiProduct;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Tests\Test\Controller\AttributesAwareEntityControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\NonCpsLimitEntityControllerValidator;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;
use Apigee\Edge\Tests\Test\Mock\TestClientFactory;

/**
 * Class ApiProductControllerTest.
 *
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
    public static function sampleDataForEntityCreate(): EntityInterface
    {
        static $entity;
        if (null === $entity) {
            $isMock = TestClientFactory::isMockClient(static::$client);
            $entity = new ApiProduct([
                'name' => $isMock ? 'phpunit_test' : 'phpunit_' . static::$random->unique()->userName,
                'displayName' => $isMock ? 'PHP Unit Test product' : static::$random->unique()->words(static::$random->numberBetween(1, 8), true),
                'approvalType' => ApiProduct::APPROVAL_TYPE_AUTO,
                'attributes' => new AttributesProperty(['foo' => 'bar']),
                'scopes' => ['scope 1', 'scope 2'],
                'quota' => 10,
                'quotaInterval' => 1,
                'quotaTimeUnit' => ApiProduct::QUOTA_INTERVAL_MINUTE,
            ]);
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public static function sampleDataForEntityUpdate(): EntityInterface
    {
        static $entity;
        if (null === $entity) {
            $isMock = TestClientFactory::isMockClient(static::$client);
            $entity = new ApiProduct([
                'displayName' => $isMock ? '(Edited) PHP Unit Test product' : static::$random->unique()->words(static::$random->numberBetween(1, 8), true),
                'approvalType' => ApiProduct::APPROVAL_TYPE_MANUAL,
                'attributes' => new AttributesProperty(['foo' => 'foo', 'bar' => 'baz']),
                'quota' => 1000,
                'quotaInterval' => 12,
                'quotaTimeUnit' => ApiProduct::QUOTA_INTERVAL_HOUR,
            ]);
        }

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
     * @depends testCreate
     */
    public function testSearchByAttribute(): void
    {
        $isMock = TestClientFactory::isMockClient(static::$client);
        /** @var ApiProductController $controller */
        $controller = $this->getEntityController();
        /** @var \Apigee\Edge\Api\Management\Entity\ApiProduct $entity */
        $entity = clone static::sampleDataForEntityCreate();
        $originalId = $entity->id();
        $unexpected = $isMock ? 'should_not_appear' : static::$random->unique()->userName;
        $entity->setName($unexpected);
        $entity->setAttribute('foo', 'foo');
        $controller->create($entity);
        static::$createdEntities[$entity->id()] = $entity;
        $ids = $controller->searchByAttribute('foo', 'bar');
        $this->assertContains($originalId, $ids);
        $this->assertNotContains($unexpected, $ids);
    }

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
}
