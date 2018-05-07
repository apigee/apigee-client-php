<?php

/*
 * Copyright 2018 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apigee\Edge\Tests\Test\Controller;

use Apigee\Edge\Tests\Test\TestClientFactory;

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
     * Data provider that returns the id field of the related entity type.
     *
     * @throws \ReflectionException
     *
     * @return array
     */
    abstract public function cpsLimitTestIdFieldProvider(): array;
}
