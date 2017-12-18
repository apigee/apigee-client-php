<?php

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Api\Management\Controller\DeveloperController;
use Apigee\Edge\Api\Management\Entity\Developer;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Tests\Test\Controller\AttributesAwareEntityControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\CpsLimitEntityControllerValidator;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;
use Apigee\Edge\Tests\Test\Mock\TestClientFactory;

/**
 * Class DeveloperControllerTest.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @group controller
 */
class DeveloperControllerTest extends CpsLimitEntityControllerValidator
{
    use AttributesAwareEntityControllerTestTrait;
    use OrganizationAwareEntityControllerValidatorTrait;

    /**
     * @inheritdoc
     */
    public static function sampleDataForEntityCreate(): EntityInterface
    {
        return new Developer([
            'email' => 'phpunit@example.com',
            'firstName' => 'Php',
            'lastName' => 'Unit',
            'userName' => 'phpunit',
            'attributes' => new AttributesProperty(['foo' => 'bar']),
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function sampleDataForEntityUpdate(): EntityInterface
    {
        return new Developer([
            'email' => 'phpunit-edited@example.com',
            'firstName' => '(Edited) Php',
            'lastName' => 'Unit',
            'userName' => 'phpunit',
            'attributes' => new AttributesProperty(['foo' => 'foo', 'bar' => 'baz']),
        ]);
    }

    /**
     * @group online
     * @expectedException \Apigee\Edge\Exception\ClientErrorException
     */
    public function testCreateWithIncorrectData(): void
    {
        if (0 === strpos(static::$client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        $entity = new Developer(['email' => 'developer-create-exception@example.com']);
        static::getEntityController()->create($entity);
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
     * We have to override this otherwise dependents of this function are being skipped.
     * Also, "@inheritdoc" is not going to work in case of "@depends" annotations so those must be repeated.
     *
     * @depends testCreate
     */
    public function testLoad(string $entityId)
    {
        return parent::testLoad($entityId);
    }

    /**
     * @depends testLoad
     *
     * @param string $entityId
     */
    public function testStatusChange(string $entityId): void
    {
        if (0 === strpos(static::$client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        $entity = static::getEntityController()->load($entityId);
        static::getEntityController()->setStatus($entity->id(), Developer::STATUS_INACTIVE);
        /** @var \Apigee\Edge\Api\Management\Entity\DeveloperInterface $entity */
        $entity = static::getEntityController()->load($entity->id());
        $this->assertEquals($entity->getStatus(), Developer::STATUS_INACTIVE);
        static::getEntityController()->setStatus($entity->id(), Developer::STATUS_ACTIVE);
        /** @var \Apigee\Edge\Api\Management\Entity\DeveloperInterface $entity */
        $entity = static::getEntityController()->load($entity->id());
        $this->assertEquals($entity->getStatus(), Developer::STATUS_ACTIVE);
    }

    /**
     * @inheritdoc
     */
    public function cpsLimitTestIdFieldProvider()
    {
        // This override makes easier the offline testing.
        return [['email']];
    }

    /**
     * @inheritdoc
     */
    protected static function getEntityController(): EntityControllerInterface
    {
        static $controller;
        if (!$controller) {
            $controller = new DeveloperController(static::getOrganization(), static::$client);
        }

        return $controller;
    }

    /**
     * @inheritdoc
     */
    protected static function expectedAfterEntityCreate(): EntityInterface
    {
        /** @var Developer $entity */
        $entity = static::sampleDataForEntityCreate();
        // We can be sure one another thing, the status of the created developer is active by default.
        $entity->setStatus(Developer::STATUS_ACTIVE);

        return $entity;
    }
}
