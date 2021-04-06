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

use Apigee\Edge\Api\ApigeeX\Entity\Property\ApiProductPropertyAwareTrait;
use Apigee\Edge\Api\ApigeeX\Entity\Property\BillingPeriodPropertyAwareTrait;
use Apigee\Edge\Api\ApigeeX\Entity\Property\ConsumptionPricingTypePropertyAwareTrait;
use Apigee\Edge\Api\ApigeeX\Entity\Property\CurrencyCodePropertyAwareTrait;
use Apigee\Edge\Api\ApigeeX\Entity\Property\EndTimePropertyAwareTrait;
use Apigee\Edge\Api\ApigeeX\Entity\Property\FixedFeeFrequencyPropertyAwareTrait;
use Apigee\Edge\Api\ApigeeX\Entity\Property\PaymentFundingModelPropertyAwareTrait;
use Apigee\Edge\Api\ApigeeX\Entity\Property\RevenueShareTypePropertyAwareTrait;
use Apigee\Edge\Api\ApigeeX\Entity\Property\StartTimePropertyAwareTrait;
use Apigee\Edge\Api\ApigeeX\Structure\ConsumptionPricingRate;
use Apigee\Edge\Api\ApigeeX\Structure\FixedRecurringFee;
use Apigee\Edge\Api\ApigeeX\Structure\RatePlanXFee;
use Apigee\Edge\Api\ApigeeX\Structure\RevenueShareRates;
use Apigee\Edge\Api\Monetization\Entity\Entity;
use Apigee\Edge\Entity\Property\DescriptionPropertyAwareTrait;
use Apigee\Edge\Entity\Property\DisplayNamePropertyAwareTrait;
use Apigee\Edge\Entity\Property\NamePropertyAwareTrait;

/**
 * Parent class for standard, developer- and developer specific rate plans.
 *
 * TODO: Add documenation link
 */
abstract class RatePlan extends Entity implements RatePlanInterface
{
    use DescriptionPropertyAwareTrait;
    use BillingPeriodPropertyAwareTrait;
    use PaymentFundingModelPropertyAwareTrait;
    use CurrencyCodePropertyAwareTrait;
    use FixedFeeFrequencyPropertyAwareTrait;
    use ConsumptionPricingTypePropertyAwareTrait;
    use RevenueShareTypePropertyAwareTrait;
    use StartTimePropertyAwareTrait;
    use EndTimePropertyAwareTrait;
    use DisplayNamePropertyAwareTrait;
    use ApiProductPropertyAwareTrait;
    use NamePropertyAwareTrait;

    /**
     * It can be null when a new rate plan is created.
     *
     * @var \Apigee\Edge\Api\ApigeeX\Entity\ApiProduct|null
     */
    protected $package;

    /** @var bool */
    protected $published = false;

    /** @var \Apigee\Edge\Api\ApigeeX\Structure\RatePlanXFee[] */
    protected $ratePlanXFee = [];

    /** @var \Apigee\Edge\Api\Monetization\ApigeeX\Structure\FixedRecurringFee[] */
    protected $fixedRecurringFee = [];

    /** @var \Apigee\Edge\Api\ApigeeX\Structure\ConsumptionPricingRate[] */
    protected $consumptionPricingRates = [];

    /** @var \Apigee\Edge\Api\ApigeeX\Structure\RevenueShareRates[] */
    protected $revenueShareRates = [];

    /**
     * {@inheritdoc}
     */
    public function getPackage(): ?ApiProductInterface
    {
        return $this->package;
    }

    /**
     * {@inheritdoc}
     */
    public function setPackage(ApiProductInterface $package): void
    {
        $this->package = $package;
    }

    /**
     * {@inheritdoc}
     */
    public function getRatePlanxFee(): array
    {
        return $this->ratePlanXFee;
    }

    /**
     * {@inheritdoc}
     */
    public function setRatePlanxFee(RatePlanXFee ...$ratePlanXFee): void
    {
        $this->ratePlanXFee = $ratePlanXFee;
    }

    /**
     * {@inheritdoc}
     */
    public function getFixedRecurringFee(): array
    {
        return $this->fixedRecurringFee;
    }

    /**
     * {@inheritdoc}
     */
    public function setFixedRecurringFee(FixedRecurringFee ...$fixedRecurringFee): void
    {
        $this->fixedRecurringFee = $fixedRecurringFee;
    }

    /**
     * {@inheritdoc}
     */
    public function getConsumptionPricingRates(): array
    {
        return $this->consumptionPricingRates;
    }

    /**
     * {@inheritdoc}
     */
    public function setConsumptionPricingRates(ConsumptionPricingRate ...$consumptionPricingRates): void
    {
        $this->consumptionPricingRates = $consumptionPricingRates;
    }

    /**
     * {@inheritdoc}
     */
    public function getRevenueShareRates(): array
    {
        return $this->revenueShareRates;
    }

    /**
     * {@inheritdoc}
     */
    public function setRevenueShareRates(RevenueShareRates ...$revenueShareRates): void
    {
        $this->revenueShareRates = $revenueShareRates;
    }
}
