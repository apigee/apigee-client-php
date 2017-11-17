<?php

namespace Apigee\Edge\Tests\Test\Controller;

use Apigee\Edge\Tests\Test\Mock\TestClientFactory;

/**
 * Class EntityControllerValidator.
 *
 * Helps in validation of all entity controllers that implements EntityControllerInterface.
 *
 * @package Apigee\Edge\Tests\Test\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see \Apigee\Edge\Entity\EntityControllerInterface
 */
abstract class EntityControllerValidator extends BaseEntityControllerValidator
{
    protected const DEFAULT_ORGANIZATION = 'phpunit';

    /** @var string */
    protected static $organization;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        self::$organization = getenv('APIGEE_PHP_SDK_ORGANIZATION') ?: self::DEFAULT_ORGANIZATION;
        parent::setUpBeforeClass();
    }

    /**
     * @depends testCreate
     */
    public function testGetEntityIds()
    {
        /** @var \Apigee\Edge\Entity\EntityControllerInterface $controller */
        $controller = $this->getEntityController();
        $this->assertNotEmpty($controller->getEntityIds());
    }

    /**
     * @dataProvider cpsLimitTestIdFieldProvider
     *
     * @param string $idField
     */
    public function testCpsLimit(string $idField)
    {
        /** @var \Apigee\Edge\Entity\EntityControllerInterface $controller */
        $controller = $this->getEntityController();
        $entity = self::$entityFactory->getEntityByController($controller);
        $values = $this->sampleDataForEntityCreate();
        $id = $values[$idField];
        $cpsTestData = [];
        for ($i = 1; $i <= 5; $i++) {
            $cpsTestData[$i] = $values;
            $cpsTestData[$i][$idField] = "{$i}{$id}";
        }
        // Create test data on the server or do not do anything if an offline client is in use.
        if (strpos(self::$client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX) === false) {
            foreach ($cpsTestData as $row) {
                $entity = $entity::fromArray($row);
                $entity = $controller->save($entity);
                self::$createdEntities[$entity->id()] = $entity;
            }
        }
        $startKey = "3{$id}";
        $limit = 2;
        $cpsLimit = $controller->createCpsLimit($startKey, $limit);
        $result = $controller->getEntityIds($cpsLimit);
        $this->assertEquals($startKey, $result[0]);
        $this->assertEquals($limit, count($result));
    }


    /**
     * @return array
     */
    public function cpsLimitTestIdFieldProvider()
    {
        $controller = $this->getEntityController();
        $entity = self::$entityFactory->getEntityByController($controller);
        return [[$entity->id()]];
    }
}
