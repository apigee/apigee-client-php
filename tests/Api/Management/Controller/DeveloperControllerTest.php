<?php

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Api\Management\Controller\DeveloperController;
use Apigee\Edge\Api\Management\Entity\Developer;
use Apigee\Edge\Entity\BaseEntityControllerInterface;
use Apigee\Edge\Tests\Test\Controller\EntityControllerValidator;
use Apigee\Edge\Tests\Test\Mock\TestClientFactory;

/**
 * Class DeveloperControllerTest.
 *
 * @package Apigee\Edge\Tests\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @group controller
 */
class DeveloperControllerTest extends EntityControllerValidator
{
    /**
     * @group online
     * @expectedException \Apigee\Edge\Exception\ClientErrorException
     */
    public function testCreateWithIncorrectData()
    {
        if (strpos(self::$client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX) === 0) {
            $this->markTestSkipped(self::$onlyOnlineClientSkipMessage);
        }
        $entity = new Developer(['email' => 'developer-create-exception@apigee.com']);
        self::getEntityController()->save($entity);
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
    public function testStatusChange(string $entityId)
    {
        if (strpos(self::$client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX) === 0) {
            $this->markTestSkipped(self::$onlyOnlineClientSkipMessage);
        }
        $entity = $this->getEntityController()->load($entityId);
        self::getEntityController()->setStatus($entity->id(), Developer::STATUS_INACTIVE);
        /** @var \Apigee\Edge\Api\Management\Entity\DeveloperInterface $entity */
        $entity = self::getEntityController()->load($entity->id());
        $this->assertEquals($entity->getStatus(), Developer::STATUS_INACTIVE);
        self::getEntityController()->setStatus($entity->id(), Developer::STATUS_ACTIVE);
        /** @var \Apigee\Edge\Api\Management\Entity\DeveloperInterface $entity */
        $entity = self::getEntityController()->load($entity->id());
        $this->assertEquals($entity->getStatus(), Developer::STATUS_ACTIVE);
    }

    /**
     * @inheritdoc
     */
    protected function sampleDataForEntityCreate(): array
    {
        return [
            'email' => 'phpunit@apigee.com',
            'firstName' => 'Php',
            'lastName' => 'Unit',
            'userName' => 'phpunit',
            'attributes' => [
                'foo' => 'bar',
            ]];
    }

    /**
     * @inheritdoc
     */
    protected function sampleDataForEntityUpdate(): array
    {
        return [
            'email' => 'phpunit-edited@apigee.com',
            'firstName' => '(Edited) Php',
            'lastName' => 'Unit',
            'userName' => 'phpunit',
            'attributes' => [
                'foo' => 'foo',
                'bar' => 'baz',
            ]];
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
    protected function expectedValuesAfterEntityCreate(): array
    {
        $values = parent::expectedValuesAfterEntityCreate();
        // We can be sure one another thing, the status of the created developer is active by default.
        $values['status'] = Developer::STATUS_ACTIVE;
        return $values;
    }

    /**
     * @inheritdoc
     */
    protected static function getEntityController(): BaseEntityControllerInterface
    {
        static $controller;
        if (!$controller) {
            $controller = new DeveloperController(static::$organization, static::$client);
        }
        return $controller;
    }
}
