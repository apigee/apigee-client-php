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
     * Data provider for testOrganizationFeatures().
     *
     * @return array
     */
    public function featurePropertyValueProvider(): array
    {
        return [
            [null, false],
            ['true', true],
            ['false', false],
        ];
    }

    /**
     * Test if an organization has pagination available.
     *
     * @dataProvider paginationAvailableValueProvider
     */
    public function testPaginationAvailable($isCpsEnabled, $isHybridEnabled, $expected, $message): void
    {
        /** @var \Apigee\Edge\Api\Management\Entity\OrganizationInterface $organization */
        $organization = $this->getMockBuilder(OrganizationInterface::class)->getMock();
        $organization->method('getPropertyValue')->will($this->returnValueMap([
            ['features.isCpsEnabled', $isCpsEnabled],
            ['features.hybrid.enabled', $isHybridEnabled],
        ]));
        $this->assertEquals($expected, OrganizationFeatures::isPaginationAvailable($organization), $message);
    }

    /**
     * Data provider for testPaginationAvailable().
     *
     * The format for each data set is: [$isCpsEnabled, $isHybridEnabled, $expected, $message]
     *
     * @return array
     */
    public function paginationAvailableValueProvider(): array
    {
        return [
            [null, 'true', true, 'Hybrid orgs should have pagination.'],
            ['true', null, true, 'CPS enabled orgs should have pagination.'],
            ['false', null, false, 'Non-Hybrid org without CPS should not have pagination.'],
        ];
    }

    /**
     * Test if an organization has companies features available.
     *
     * @dataProvider companiesAvailableValueProvider
     */
    public function testCompaniesAvailable($isHybridEnabled, $expected, $message): void
    {
        /** @var \Apigee\Edge\Api\Management\Entity\OrganizationInterface $organization */
        $organization = $this->getMockBuilder(OrganizationInterface::class)->getMock();
        $organization->method('getPropertyValue')->willReturn($isHybridEnabled);
        $this->assertEquals($expected, OrganizationFeatures::isCompaniesFeatureAvailable($organization), $message);
    }

    /**
     * Data provider for testCompaniesAvailable().
     *
     * @return array
     */
    public function companiesAvailableValueProvider(): array
    {
        // Format: ['features.hybrid.enabled', $isCompaniesFeatureAvailable]
        return [
            [null, true, 'Non-hybrid organizations should have companies feature available.'],
            ['true', false, 'Hybrid organizations should not have companies feature available.'],
        ];
    }
}
