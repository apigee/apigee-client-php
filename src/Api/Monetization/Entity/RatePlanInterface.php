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

namespace Apigee\Edge\Api\Monetization\Entity;

use Apigee\Edge\Api\Monetization\Entity\Property\CurrencyPropertyInterface;
use Apigee\Edge\Api\Monetization\Entity\Property\EndDatePropertyInterface;
use Apigee\Edge\Api\Monetization\Entity\Property\FreemiumPropertiesInterface;
use Apigee\Edge\Api\Monetization\Entity\Property\PaymentDueDaysPropertyInterface;
use Apigee\Edge\Api\Monetization\Entity\Property\StartDatePropertyInterface;
use Apigee\Edge\Api\Monetization\Structure\RatePlanDetail;
use Apigee\Edge\Entity\Property\DescriptionPropertyInterface;
use Apigee\Edge\Entity\Property\DisplayNamePropertyInterface;
use Apigee\Edge\Entity\Property\NamePropertyInterface;

interface RatePlanInterface extends
    OrganizationAwareEntityInterface,
    CurrencyPropertyInterface,
    DescriptionPropertyInterface,
    DisplayNamePropertyInterface,
    EndDatePropertyInterface,
    FreemiumPropertiesInterface,
    NamePropertyInterface,
    PaymentDueDaysPropertyInterface,
    StartDatePropertyInterface
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
     * @return bool
     */
    public function isAdvance(): bool;

    /**
     * @param bool $advance
     */
    public function setAdvance(bool $advance): void;

    /**
     * @return int|null
     */
    public function getContractDuration(): ?int;

    /**
     * @param int $contractDuration
     */
    public function setContractDuration(int $contractDuration): void;

    /**
     * @return string|null
     */
    public function getContractDurationType(): ?string;

    /**
     * @param string $contractDurationType
     */
    public function setContractDurationType(string $contractDurationType): void;

    /**
     * @return float|null
     */
    public function getEarlyTerminationFee(): ?float;

    /**
     * @param float $earlyTerminationFee
     */
    public function setEarlyTerminationFee(float $earlyTerminationFee): void;

    /**
     * @return int|null
     */
    public function getFrequencyDuration(): ?int;

    /**
     * @param int $frequencyDuration
     */
    public function setFrequencyDuration(int $frequencyDuration): void;

    /**
     * @return string|null
     */
    public function getFrequencyDurationType(): ?string;

    /**
     * @param string $frequencyDurationType
     */
    public function setFrequencyDurationType(string $frequencyDurationType): void;

    /**
     * @return bool
     */
    public function isPrivate(): bool;

    /**
     * @param bool $private
     */
    public function setPrivate(bool $private): void;

    /**
     * It could be null only when a rate plan is created.
     *
     * @return ApiPackageInterface|null
     */
    public function getPackage(): ?ApiPackageInterface;

    /**
     * @param ApiPackageInterface $package
     */
    public function setPackage(ApiPackageInterface $package): void;

    /**
     * @return bool
     */
    public function isProrate(): bool;

    /**
     * @param bool $prorate
     */
    public function setProrate(bool $prorate): void;

    /**
     * @return bool
     */
    public function isPublished(): bool;

    /**
     * @param bool $published
     */
    public function setPublished(bool $published): void;

    /**
     * @return \Apigee\Edge\Api\Monetization\Structure\RatePlanDetail[]
     */
    public function getRatePlanDetails(): array;

    /**
     * @param RatePlanDetail ...$ratePlanDetails
     */
    public function setRatePlanDetails(RatePlanDetail ...$ratePlanDetails): void;

    /**
     * @return float|null
     */
    public function getRecurringFee(): ?float;

    /**
     * @param float $recurringFee
     */
    public function setRecurringFee(float $recurringFee): void;

    /**
     * @return int|null
     */
    public function getRecurringStartUnit(): ?int;

    /**
     * @param int $recurringStartUnit
     */
    public function setRecurringStartUnit(int $recurringStartUnit): void;

    /**
     * @return string
     */
    public function getRecurringType(): string;

    /**
     * @param string $recurringType
     */
    public function setRecurringType(string $recurringType): void;

    /**
     * @return float
     */
    public function getSetUpFee(): float;

    /**
     * @param float $setUpFee
     */
    public function setSetUpFee(float $setUpFee): void;
}
