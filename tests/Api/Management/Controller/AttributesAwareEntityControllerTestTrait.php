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

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Exception\ApiResponseException;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerAwareTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationTestControllerAwareTrait;
use Apigee\Edge\Tests\Test\Entity\NewEntityProviderTrait;
use Apigee\Edge\Tests\Test\Utility\MarkOnlineTestSkippedAwareTrait;
use DMS\PHPUnitExtensions\ArraySubset\Assert;

trait AttributesAwareEntityControllerTestTrait
{
    use DefaultAPIClientAwareTrait;
    use EntityCreateOperationTestControllerAwareTrait;
    use EntityControllerAwareTestTrait;
    use NewEntityProviderTrait;
    use MarkOnlineTestSkippedAwareTrait;

    public function testAddAttributesToEntity(): string
    {
        /** @var \Apigee\Edge\Api\Management\Controller\AttributesAwareEntityControllerInterface $controller */
        $controller = $this->entityController();
        /** @var \Apigee\Edge\Entity\Property\AttributesPropertyInterface|\Apigee\Edge\Entity\EntityInterface $entity */
        $entity = static::getNewEntity();
        static::entityCreateOperationTestController()->create($entity);
        /** @var \Apigee\Edge\Structure\AttributesProperty $attributes */
        $attributes = $entity->getAttributes();
        $originalAttributes = $attributes->values();
        $attributes->add('name1', 'value1');
        $attributes->add('name2', 'value2');
        /** @var \Apigee\Edge\Structure\AttributesProperty $attributesProperty */
        $attributesProperty = $controller->updateAttributes($entity->id(), $attributes);
        /** @var array $newAttributes */
        $newAttributes = $attributesProperty->values();
        Assert::assertArraySubset($originalAttributes, $newAttributes);
        $this->assertArrayHasKey('name1', $newAttributes);
        $this->assertArrayHasKey('name2', $newAttributes);
        $this->assertEquals('value1', $newAttributes['name1']);
        $this->assertEquals('value2', $newAttributes['name2']);

        return $entity->id();
    }

    /**
     * @depends testAddAttributesToEntity
     *
     * @param string $entityId
     */
    public function testGetAttributes(string $entityId): void
    {
        /** @var \Apigee\Edge\Api\Management\Controller\AttributesAwareEntityControllerInterface $controller */
        $controller = $this->entityController();
        /** @var \Apigee\Edge\Structure\AttributesProperty $attributesProperty */
        $attributesProperty = $controller->getAttributes($entityId);
        $attributes = $attributesProperty->values();
        $this->assertNotEmpty($attributes);
        $this->assertArrayHasKey('name1', $attributes);
        $this->assertArrayHasKey('name2', $attributes);
        $this->assertEquals('value1', $attributes['name1']);
        $this->assertEquals('value2', $attributes['name2']);
    }

    /**
     * @depends testAddAttributesToEntity
     *
     * @param string $entityId
     */
    public function testGetAttribute(string $entityId): void
    {
        /** @var \Apigee\Edge\Api\Management\Controller\AttributesAwareEntityControllerInterface $controller */
        $controller = $this->entityController();
        $value = $controller->getAttribute($entityId, 'name1');
        $this->assertEquals('value1', $value);
    }

    /**
     * @depends testAddAttributesToEntity
     *
     * @param string $entityId
     *
     * @return string
     */
    public function testUpdateAttribute(string $entityId): string
    {
        /** @var \Apigee\Edge\Api\Management\Controller\AttributesAwareEntityControllerInterface $controller */
        $controller = $this->entityController();
        $expected = 'value1-edited';
        $value = $controller->updateAttribute($entityId, 'name1', $expected);
        $this->assertEquals($expected, $value);

        return $entityId;
    }

    /**
     * @depends testUpdateAttribute
     *
     * @group online
     *
     * @param string $entityId
     */
    public function testDeleteAttribute(string $entityId): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        /** @var \Apigee\Edge\Api\Management\Controller\AttributesAwareEntityControllerInterface $controller */
        $controller = $this->entityController();
        $attribute = 'name1';
        $controller->deleteAttribute($entityId, $attribute);
        try {
            $controller->getAttribute($entityId, $attribute);
        } catch (ApiResponseException $e) {
            $this->assertEquals('organizations.keymanagement.AttributeDoesntExist', $e->getEdgeErrorCode());
        }
    }
}
