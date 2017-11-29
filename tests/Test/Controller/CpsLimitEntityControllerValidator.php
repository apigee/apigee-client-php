<?php

namespace Apigee\Edge\Tests\Test\Controller;

use Apigee\Edge\Tests\Test\Mock\TestClientFactory;

/**
 * Trait CpsLimitEntityControllerTest.
 *
 * Helps in validation of those entity controllers that implements CpsLimitEntityControllerInterface.
 *
 * @package Apigee\Edge\Tests\Test\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see \Apigee\Edge\Entity\CpsLimitEntityControllerInterface
 */
abstract class CpsLimitEntityControllerValidator extends EntityCrudOperationsValidator
{
    /**
     * @dataProvider cpsLimitTestIdFieldProvider
     *
     * @param string $idField
     */
    public function testCpsLimit(string $idField)
    {
        /** @var \Apigee\Edge\Entity\EntityCrudOperationsInterface $controller */
        $controller = $this->getEntityController();
        $sampleEntity = $this->sampleDataForEntityCreate();
        $sampleEntityId = call_user_func([$sampleEntity, 'get' . $idField]);
        $cpsTestData = [];
        for ($i = 1; $i <= 5; $i++) {
            $cpsTestData[$i] = clone $sampleEntity;
            $cpsTestData[$i]->{'set' . $idField}($i.$sampleEntityId);
        }
        // Create test data on the server or do not do anything if an offline client is in use.
        if (strpos(self::$client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX) === false) {
            foreach ($cpsTestData as $item) {
                /** @var \Apigee\Edge\Entity\EntityInterface $item */
                $tmp = $controller->create($item);
                self::$createdEntities[$tmp->id()] = $tmp;
            }
        }
        $startKey = "3{$sampleEntityId}";
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
