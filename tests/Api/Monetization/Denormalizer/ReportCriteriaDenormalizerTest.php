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

namespace Apigee\Edge\Tests\Api\Monetization\Denormalizer;

use Apigee\Edge\Api\Monetization\Denormalizer\ReportCriteriaDenormalizer;
use Apigee\Edge\Api\Monetization\Entity\EntityInterface;
use Apigee\Edge\Api\Monetization\Entity\ReportDefinitionInterface;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\AbstractCriteria;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\BillingReportCriteria;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\PrepaidBalanceReportCriteria;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\RevenueReportCriteria;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

class ReportCriteriaDenormalizerTest extends TestCase
{
    /** @var Apigee\Edge\Api\Monetization\Denormalizer\ReportCriteriaDenormalizer */
    protected static $denormalizer;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$denormalizer = new ReportCriteriaDenormalizer();
        static::$denormalizer->setSerializer(new Serializer([new DateTimeNormalizer([DateTimeNormalizer::FORMAT_KEY => EntityInterface::DATE_FORMAT])]));
    }

    public function testDenormalizeWithAbtractClassNoContext(): void
    {
        // $this->expectException('\Symfony\Component\Serializer\Exception\NotNormalizableValueException');

        static::$denormalizer->denormalize((object) [], AbstractCriteria::class, 'json');
    }

    public function testDenormalizeWithAbstractClassUnknownReportType(): void
    {
        $this->expectException('\Apigee\Edge\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('Invalid report definition type: foo.');

        static::$denormalizer->denormalize((object) [], AbstractCriteria::class, 'json', [ReportCriteriaDenormalizer::CONTEXT_REPORT_DEFINITION_TYPE => 'foo']);
    }

    /**
     * @dataProvider denormalizeWithTypeDataProvider
     */
    public function testDenormalizeWithKnownTypes(string $reportType, string $expectedClass, array $constructArgs): void
    {
        $this->assertInstanceOf($expectedClass, static::$denormalizer->denormalize((object) $constructArgs, AbstractCriteria::class, 'json', [ReportCriteriaDenormalizer::CONTEXT_REPORT_DEFINITION_TYPE => $reportType]));
    }

    public function testDataDenormalization(): void
    {
        $data = (object) [
            'billingMonth' => 'JANUARY', 'billingYear' => 2019,
            'pkgCriteria' => (object) [
                [
                    'id' => 'fooPkg',
                    'orgId' => 'orgId',
                ],
                [
                    'id' => 'barPkg',
                    'orgId' => 'orgId',
                ],
            ],
            'monetizationPackageIds' => ['barPkg', 'bazPkg'],
            'prodCriteria' => (object) [
                [
                    'id' => 'fooProd',
                    'orgId' => 'orgId',
                ],
                [
                    'id' => 'barProd',
                    'orgId' => 'orgId',
                ],
            ],
            'productIds' => ['barProd', 'bazProd'],
        ];

        $obj = static::$denormalizer->denormalize($data, AbstractCriteria::class, 'json', [ReportCriteriaDenormalizer::CONTEXT_REPORT_DEFINITION_TYPE => ReportDefinitionInterface::TYPE_BILLING]);
        $this->assertEquals(['barPkg', 'bazPkg', 'fooPkg'], $obj->getApiPackages());
        $this->assertEquals(['barProd', 'bazProd', 'fooProd'], $obj->getApiProducts());
    }

    public function denormalizeWithTypeDataProvider(): array
    {
        return [
            [ReportDefinitionInterface::TYPE_BILLING, BillingReportCriteria::class, ['billingMonth' => 'JANUARY', 'billingYear' => 2019]],
            [ReportDefinitionInterface::TYPE_PREPAID_BALANCE, PrepaidBalanceReportCriteria::class, ['billingMonth' => 'JANUARY', 'billingYear' => 2019]],
            [ReportDefinitionInterface::TYPE_REVENUE, RevenueReportCriteria::class, ['fromDate' => '2015-07-01 00:00:00', 'toDate' => '2015-08-01 13:35:00']],
        ];
    }
}
