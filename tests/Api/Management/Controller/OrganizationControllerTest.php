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

use Apigee\Edge\Api\Management\Controller\OrganizationController;
use Apigee\Edge\Tests\Test\Controller\ControllerTestBase;
use Apigee\Edge\Tests\Test\Controller\FileSystemMockAPIClientAwareTrait;

/**
 * Class OrganizationControllerTest.
 *
 * This test only covers the "Get Organization" API call, because that is the
 * only one which is available in Apigee Edge Public Cloud. Also other
 * API calls like delete and create, should not be used because these operations
 * usually require extra configurations that can not be solved by
 * simple Management API calls.
 *
 * @see https://docs.apigee.com/api-services/latest/creating-organization-environment-and-virtual-host
 *
 * @group controller
 * @group management
 */
class OrganizationControllerTest extends ControllerTestBase
{
    use FileSystemMockAPIClientAwareTrait;

    public function testLoad(): void
    {
        $controller = new OrganizationController(static::fileSystemMockClient());
        /** @var \Apigee\Edge\Api\Management\Entity\OrganizationInterface $entity */
        $entity = $controller->load('phpunit');
        $this->assertEquals('PHPUnit', $entity->getDisplayName());
        $this->assertEquals(['prod', 'test'], $entity->getEnvironments());
        $this->assertTrue($entity->hasProperty('self.service.virtual.host.enabled'));
        $this->assertEquals('true', $entity->getPropertyValue('features.isCpsEnabled'));
        $this->assertEmpty($entity->getPropertyValue('features.hybrid.enabled'));
        $this->assertEquals('trial', $entity->getType());
        $this->assertEquals(new \DateTimeImmutable('@648345600'), $entity->getCreatedAt());
        $this->assertEquals(new \DateTimeImmutable('@648345600'), $entity->getLastModifiedAt());
        $this->assertEquals('phpunit@example.com', $entity->getCreatedBy());
        $this->assertEquals('phpunit@example.com', $entity->getLastModifiedBy());
        $this->assertFalse($entity->isHybrid());
    }

    /**
     * Tests loading a Hybrid Organization.
     */
    public function testLoadHybrid(): void
    {
        $controller = new OrganizationController(static::fileSystemMockClient());
        /** @var \Apigee\Edge\Api\Management\Entity\OrganizationInterface $entity */
        $entity = $controller->load('hybridorg');
        $this->assertEquals('Hybrid Org', $entity->getDisplayName());
        $this->assertEquals(['prod', 'test'], $entity->getEnvironments());
        $this->assertTrue($entity->hasProperty('features.hybrid.enabled'));
        $this->assertEquals('true', $entity->getPropertyValue('features.hybrid.enabled'));
        $this->assertEmpty($entity->getPropertyValue('features.isCpsEnabled'));
        $this->assertEquals(new \DateTimeImmutable('@1565122730'), $entity->getCreatedAt());
        $this->assertEquals(new \DateTimeImmutable('@1568828772'), $entity->getLastModifiedAt());
        $this->assertTrue($entity->isHybrid());
    }
}
