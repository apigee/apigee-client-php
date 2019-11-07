<?php

/*
 * Copyright 2019 Google LLC
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

namespace Apigee\Edge\Tests\Utility;

use Apigee\Edge\Api\Management\Entity\OrganizationInterface;
use Apigee\Edge\Utility\OrganizationFeatures;
use PHPUnit\Framework\TestCase;

/**
 * Class OrganizationFeaturesTest.
 *
 * @small
 */
class OrganizationFeaturesTest extends TestCase
{
    /**
     * Tests simple properties on the organization.
     *
     * @dataProvider featurePropertyValueProvider
     */
    public function testOrganizationFeatures(?string $propertyValue, bool $expectedResult): void
    {
        /** @var \Apigee\Edge\Api\Management\Entity\OrganizationInterface $organization */
        $organization = $this->getMockBuilder(OrganizationInterface::class)->getMock();
        $organization->method('getPropertyValue')->willReturn($propertyValue);
        $this->assertEquals($expectedResult, OrganizationFeatures::isCpsEnabled($organization));
        $this->assertEquals($expectedResult, OrganizationFeatures::isHybridEnabled($organization));
        $this->assertEquals($expectedResult, OrganizationFeatures::isMonetizationEnabled($organization));
    }

    /**
     * Test if an organization has pagination enabled.
     */
    public function testPaginationEnabled(): void
    {
        /** @var \Apigee\Edge\Api\Management\Entity\OrganizationInterface $organization */
        $organization = $this->getMockBuilder(OrganizationInterface::class)->getMock();
        $organization->method('getPropertyValue')->will($this->returnValueMap([
            ['features.isCpsEnabled', null],
            ['features.hybrid.enabled', 'true'],
        ]));
        $this->assertTrue(OrganizationFeatures::isPaginationEnabled($organization));

        $organization = $this->getMockBuilder(OrganizationInterface::class)->getMock();
        $organization->method('getPropertyValue')->will($this->returnValueMap([
            ['features.isCpsEnabled', 'true'],
            ['features.hybrid.enabled', null],
        ]));
        $this->assertTrue(OrganizationFeatures::isPaginationEnabled($organization));

        $organization = $this->getMockBuilder(OrganizationInterface::class)->getMock();
        $organization->method('getPropertyValue')->will($this->returnValueMap([
            ['features.isCpsEnabled', 'false'],
            ['features.hybrid.enabled', null],
        ]));
        $this->assertFalse(OrganizationFeatures::isPaginationEnabled($organization));
    }

    public function featurePropertyValueProvider(): array
    {
        return [
            [null, false],
            ['true', true],
            ['false', false],
        ];
    }
}
