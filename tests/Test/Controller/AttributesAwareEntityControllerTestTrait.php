<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Tests\Test\Controller;

use Apigee\Edge\Exception\ApiResponseException;
use Apigee\Edge\Tests\Test\Mock\TestClientFactory;

/**
 * Trait AttributesAwareEntityControllerTestTrait.
 */
trait AttributesAwareEntityControllerTestTrait
{
    /**
     * @depends testUpdate
     *
     * @param string $entityId
     *
     * @return string
     */
    public function testAddAttributesToEntity(string $entityId)
    {
        /** @var \Apigee\Edge\Api\Management\Controller\AttributesAwareEntityControllerInterface $controller */
        $controller = $this->getEntityController();
        /** @var \Apigee\Edge\Entity\Property\AttributesPropertyAwareTrait $entity */
        $entity = $controller->load($entityId);
        /** @var \Apigee\Edge\Structure\AttributesProperty $attributes */
        $attributes = $entity->getAttributes();
        $originalAttributes = $attributes->values();
        $attributes->add('name1', 'value1');
        $attributes->add('name2', 'value2');
        /** @var \Apigee\Edge\Structure\AttributesProperty $attributesProperty */
        $attributesProperty = $controller->updateAttributes($entity->id(), $attributes);
        /** @var array $newAttributes */
        $newAttributes = $attributesProperty->values();
        $this->assertArraySubset($originalAttributes, $newAttributes);
        $this->assertArrayHasKey('name1', $newAttributes);
        $this->assertArrayHasKey('name2', $newAttributes);
        $this->assertEquals('value1', $newAttributes['name1']);
        $this->assertEquals('value2', $newAttributes['name2']);

        return $entityId;
    }

    /**
     * @depends testAddAttributesToEntity
     *
     * @param string $entityId
     */
    public function testGetAttributes(string $entityId): void
    {
        /** @var \Apigee\Edge\Api\Management\Controller\AttributesAwareEntityControllerInterface $controller */
        $controller = $this->getEntityController();
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
        $controller = $this->getEntityController();
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
        $controller = $this->getEntityController();
        $expected = 'value1-edited';
        $value = $controller->updateAttribute($entityId, 'name1', $expected);
        $this->assertEquals($expected, $value);

        return $entityId;
    }

    /**
     * @depends testUpdateAttribute
     *
     * @param string $entityId
     */
    public function testDeleteAttribute(string $entityId): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        /** @var \Apigee\Edge\Api\Management\Controller\AttributesAwareEntityControllerInterface $controller */
        $controller = $this->getEntityController();
        $attribute = 'name1';
        $controller->deleteAttribute($entityId, 'name1', $attribute);
        try {
            $controller->getAttribute($entityId, $attribute);
        } catch (ApiResponseException $e) {
            $this->assertEquals('organizations.keymanagement.AttributeDoesntExist', $e->getEdgeErrorCode());
        }
    }
}
