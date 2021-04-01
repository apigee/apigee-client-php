<?php

/*
 * Copyright 2021 Google LLC
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

namespace Apigee\Edge\Api\ApigeeX\Entity;

use Apigee\Edge\Api\Monetization\Entity\EntityInterface;
use Apigee\Edge\Entity\Property\DescriptionPropertyInterface;
use Apigee\Edge\Entity\Property\DisplayNamePropertyInterface;
use Apigee\Edge\Entity\Property\NamePropertyInterface;
use Apigee\Edge\Api\ApigeeX\Entity\Property\ApiProductPropertyInterface;
use Apigee\Edge\Api\ApigeeX\Structure\RatePlanXFee;
use Apigee\Edge\Api\ApigeeX\Structure\FixedRecurringFee;
use Apigee\Edge\Api\ApigeeX\Structure\ConsumptionPricingRate;
use Apigee\Edge\Api\ApigeeX\Structure\RevenueShareRates;

interface RatePlanInterface extends
    EntityInterface,
    DescriptionPropertyInterface,
    DisplayNamePropertyInterface,
    ApiProductPropertyInterface,
    NamePropertyInterface
{
    public const FREQUENCY_DURATION_DAY = 'DAY';

    public const FREQUENCY_DURATION_WEEK = 'WEEK';

    public const FREQUENCY_DURATION_MONTH = 'MONTH';

    public const FREQUENCY_DURATION_QUARTER = 'QUARTER';

    public const RECURRING_TYPE_CALENDAR = 'CALENDAR';

    public const RECURRING_TYPE_CUSTOM = 'CUSTOM';

    public const TYPE_STANDARD = 'STANDARD';

    public const TYPE_DEVELOPER = 'DEVELOPER';

    public const TYPE_DEVELOPER_CATEGORY = 'DEVELOPER_CATEGORY';

    /**
     * It could be null only when a rate plan is created.
     *
     * @return \Apigee\Edge\Api\ApigeeX\Entity\ApiProductInterface|null
     */
    public function getPackage(): ?ApiProductInterface;

    /**
     * @param \Apigee\Edge\Api\ApigeeX\Entity\ApiProductInterface $package
     */
    public function setPackage(ApiProductInterface $package): void;

    /**
     * @return \Apigee\Edge\Api\ApigeeX\Structure\RatePlanXFee[]
     */
    public function getRatePlanXFee(): array;

    /**
     * @param \Apigee\Edge\Api\ApigeeX\Structure\RatePlanXFee ...$ratePlanXFee
     */
    public function setRatePlanXFee(RatePlanXFee ...$ratePlanXFee): void;

    /**
     * @return \Apigee\Edge\Api\ApigeeX\Structure\FixedRecurringFee[]
     */
    public function getFixedRecurringFee(): array;

    /**
     * @param \Apigee\Edge\Api\ApigeeX\Structure\FixedRecurringFee ...$fixedRecurringFee
     */
    public function setFixedRecurringFee(FixedRecurringFee ...$fixedRecurringFee): void;

    /**
     * @return \Apigee\Edge\Api\ApigeeX\Structure\ConsumptionPricingRate[]
     */
    public function getConsumptionPricingRates(): array;

    /**
     * @param \Apigee\Edge\Api\ApigeeX\Structure\ConsumptionPricingRate ...$consumptionPricingRates
     */
    public function setConsumptionPricingRates(ConsumptionPricingRate ...$consumptionPricingRates): void;

    /**
     * @return \Apigee\Edge\Api\ApigeeX\Structure\RevenueShareRates[]
     */
    public function getRevenueShareRates(): array;

    /**
     * @param \Apigee\Edge\Api\ApigeeX\Structure\RevenueShareRates ...$revenueShareRates
     */
    public function setRevenueShareRates(RevenueShareRates ...$revenueShareRates): void;
}
