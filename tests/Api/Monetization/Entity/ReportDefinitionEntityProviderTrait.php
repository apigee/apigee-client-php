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

namespace Apigee\Edge\Tests\Api\Monetization\Entity;

use Apigee\Edge\Api\Monetization\Entity\ReportDefinition;
use Apigee\Edge\Api\Monetization\Entity\ReportDefinitionInterface;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\BillingReportCriteria;
use Apigee\Edge\Tests\Test\Utility\RandomGeneratorAwareTrait;

trait ReportDefinitionEntityProviderTrait
{
    use RandomGeneratorAwareTrait;

    protected static function getNewReportDefinition(bool $randomData = true): ReportDefinitionInterface
    {
        $criteria = new BillingReportCriteria('JANUARY', $randomData ? date('Y') : 2019);
        $criteria->setGroupBy('PACKAGE', 'PRODUCT', 'DEVELOPER')->setRatePlanLevels('STANDARD', 'DEVELOPER')->setDevelopers('developer@example.com');
        $entity = new ReportDefinition([
            'name' => $randomData ? static::randomGenerator()->machineName() : 'PHPUnit',
            'description' => $randomData ? static::randomGenerator()->text() : 'test report definition provider',
            'criteria' => $criteria,
        ]);

        return $entity;
    }

    protected static function getUpdatedReportDefinition(ReportDefinitionInterface $existing, bool $randomData = true): ReportDefinitionInterface
    {
        $updated = clone $existing;
        $updated->setDescription($randomData ? static::randomGenerator()->text() : '(edited) test report definition provider');
        /** @var BillingReportCriteria $criteria */
        $criteria = $updated->getCriteria();
        $criteria->setBillingMonth('FEBRUARY');
        $criteria->setShowSummary(!$criteria->getShowSummary());
        $updated->setCriteria($criteria);

        return $updated;
    }
}
