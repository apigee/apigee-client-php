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
use Apigee\Edge\Utility\OrganisationFeatures;
use PHPUnit\Framework\TestCase;

/**
 * Class OrganisationFeaturesTest.
 *
 * @small
 */
class OrganisationFeaturesTest extends TestCase
{
    /**
     * @dataProvider featurePropertyValueProvider
     */
    public function testOrganisationFeatures(?string $propertyValue, bool $expectedResult): void
    {
        /** @var \Apigee\Edge\Api\Management\Entity\OrganizationInterface $organisation */
        $organisation = $this->getMockBuilder(OrganizationInterface::class)->getMock();
        $organisation->method('getPropertyValue')->willReturn($propertyValue);
        $this->assertEquals($expectedResult, OrganisationFeatures::isCpsEnabled($organisation));
        $this->assertEquals($expectedResult, OrganisationFeatures::isHybridEnabled($organisation));
        $this->assertEquals($expectedResult, OrganisationFeatures::isMonetizationEnabled($organisation));
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
