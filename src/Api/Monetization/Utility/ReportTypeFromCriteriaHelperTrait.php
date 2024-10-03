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

namespace Apigee\Edge\Api\Monetization\Utility;

use Apigee\Edge\Api\Monetization\Entity\ReportDefinitionInterface;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\AbstractCriteria;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\BillingReportCriteria;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\PrepaidBalanceReportCriteria;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\RevenueReportCriteria;
use Apigee\Edge\Exception\InvalidArgumentException;

trait ReportTypeFromCriteriaHelperTrait
{
    /**
     * Gets the type of a report definition based on the criteria in it.
     *
     * @param AbstractCriteria $criteria
     *
     * @throws \Apigee\Edge\Exception\RuntimeException
     *   If report type could not be identified.
     *
     * @return string
     *   The type of a report.
     *
     * @see ReportDefinitionInterface
     */
    final protected function getReportTypeFromCriteria(AbstractCriteria $criteria): string
    {
        switch (get_class($criteria)) {
            case BillingReportCriteria::class:
                $type = ReportDefinitionInterface::TYPE_BILLING;
                break;
            case PrepaidBalanceReportCriteria::class:
                $type = ReportDefinitionInterface::TYPE_PREPAID_BALANCE;
                break;
            case RevenueReportCriteria::class:
                $type = ReportDefinitionInterface::TYPE_REVENUE;
                break;
            default:
                throw new InvalidArgumentException('Unable to identify report type.');
        }

        return $type;
    }
}
