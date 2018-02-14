<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Tests\Test\Controller;

use Apigee\Edge\Tests\Test\Mock\TestClientFactory;

/**
 * Class CpsLimitEntityControllerValidator.
 *
 * Helps in validation of those entity controllers that implements CpsLimitEntityControllerInterface.
 *
 *
 * @see \Apigee\Edge\Entity\CpsLimitEntityControllerInterface
 */
abstract class CpsLimitEntityControllerValidator extends EntityCrudOperationsControllerValidator
{
    /**
     * @dataProvider cpsLimitTestIdFieldProvider
     *
     * @param string $idField
     */
    public function testCpsLimit(string $idField): void
    {
        /** @var \Apigee\Edge\Controller\CpsLimitEntityControllerInterface $controller */
        $controller = $this->getEntityController();
        $sampleEntity = static::sampleDataForEntityCreate();
        $sampleEntityId = call_user_func([$sampleEntity, 'get' . $idField]);
        $cpsTestData = [];
        for ($i = 1; $i <= 5; ++$i) {
            $cpsTestData[$i] = clone $sampleEntity;
            $cpsTestData[$i]->{'set' . $idField}($i . $sampleEntityId);
        }
        // Create test data on the server or do not do anything if an offline client is in use.
        if (!TestClientFactory::isMockClient(static::$client)) {
            foreach ($cpsTestData as $item) {
                /* @var \Apigee\Edge\Controller\EntityCrudOperationsControllerInterface $item */
                $controller->create($item);
                static::$createdEntities[$item->id()] = $item;
            }
        }
        $startKey = "3{$sampleEntityId}";
        $limit = 2;
        $cpsLimit = $controller->createCpsLimit($startKey, $limit);
        $result = $controller->getEntityIds($cpsLimit);
        $this->assertEquals($startKey, $result[0]);
        $this->assertCount($limit, $result);
    }

    /**
     * @return array
     */
    public function cpsLimitTestIdFieldProvider()
    {
        $controller = $this->getEntityController();
        $entity = static::$entityFactory->getEntityByController($controller);

        return [[$entity->id()]];
    }
}
