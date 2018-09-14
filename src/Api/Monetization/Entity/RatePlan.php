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

use Apigee\Edge\Api\Monetization\Entity\Property\CurrencyPropertyAwareTrait;
use Apigee\Edge\Api\Monetization\Entity\Property\EndDatePropertyAwareTrait;
use Apigee\Edge\Api\Monetization\Entity\Property\FreemiumPropertiesAwareTrait;
use Apigee\Edge\Api\Monetization\Entity\Property\PaymentDueDaysPropertyAwareTrait;
use Apigee\Edge\Api\Monetization\Entity\Property\StartDatePropertyAwareTrait;
use Apigee\Edge\Entity\Property\DescriptionPropertyAwareTrait;
use Apigee\Edge\Entity\Property\DisplayNamePropertyAwareTrait;
use Apigee\Edge\Entity\Property\NamePropertyAwareTrait;

/**
 * Parent class for standard, developer- and developer specific rate plans.
 *
 * @see https://docs.apigee.com/api-platform/monetization/create-rate-plans#creatingrateplansusingtheapi-configurationsettingsforrateplans
 */
abstract class RatePlan extends OrganizationAwareEntity implements RatePlanInterface
{
    use CurrencyPropertyAwareTrait;
    use DescriptionPropertyAwareTrait;
    use DisplayNamePropertyAwareTrait;
    use EndDatePropertyAwareTrait;
    use FreemiumPropertiesAwareTrait;
    use NamePropertyAwareTrait;
    use PaymentDueDaysPropertyAwareTrait;
    use StartDatePropertyAwareTrait;

    /** @var bool */
    protected $advance = false;

    /** @var null|int */
    protected $contractDuration;

    /** @var null|string */
    protected $contractDurationType;

    /** @var null|float */
    protected $earlyTerminationFee;

    /** @var null|int */
    protected $frequencyDuration;

    /** @var null|string */
    protected $frequencyDurationType;

    /**
     * Value of "isPrivate" from the API response.
     *
     * @var bool
     */
    protected $private = false;

    /**
     * Value of "monetizationPackage" from the API response.
     *
     * It can be null when a new entity is created.
     *
     * @var null|\Apigee\Edge\Api\Monetization\Entity\ApiPackage
     */
    protected $package;

    /** @var bool */
    protected $prorate = false;

    /** @var bool */
    protected $published = false;

    /** @var \Apigee\Edge\Api\Monetization\Structure\RatePlanDetail[] */
    protected $ratePlanDetails = [];

    /** @var null|float */
    protected $recurringFee;

    /** @var null|int */
    protected $recurringStartUnit;

    /** @var string */
    protected $recurringType = self::RECURRING_TYPE_CALENDAR;

    /** @var float */
    protected $setUpFee = 0.0;

    /**
     * @inheritdoc
     */
    public function isAdvance(): bool
    {
        return $this->advance;
    }

    /**
     * @inheritdoc
     */
    public function setAdvance(bool $advance): void
    {
        $this->advance = $advance;
    }

    /**
     * @inheritdoc
     */
    public function getContractDuration(): ?int
    {
        return $this->contractDuration;
    }

    /**
     * @inheritdoc
     */
    public function setContractDuration(int $contractDuration): void
    {
        $this->contractDuration = $contractDuration;
    }

    /**
     * @inheritdoc
     */
    public function getContractDurationType(): ?string
    {
        return $this->contractDurationType;
    }

    /**
     * @inheritdoc
     */
    public function setContractDurationType(string $contractDurationType): void
    {
        $this->contractDurationType = $contractDurationType;
    }

    /**
     * @inheritdoc
     */
    public function getEarlyTerminationFee(): ?float
    {
        return $this->earlyTerminationFee;
    }

    /**
     * @inheritdoc
     */
    public function setEarlyTerminationFee(float $earlyTerminationFee): void
    {
        $this->earlyTerminationFee = $earlyTerminationFee;
    }

    /**
     * @inheritdoc
     */
    public function getFrequencyDuration(): ?int
    {
        return $this->frequencyDuration;
    }

    /**
     * @inheritdoc
     */
    public function setFrequencyDuration(int $frequencyDuration): void
    {
        $this->frequencyDuration = $frequencyDuration;
    }

    /**
     * @inheritdoc
     */
    public function getFrequencyDurationType(): ?string
    {
        return $this->frequencyDurationType;
    }

    /**
     * @inheritdoc
     */
    public function setFrequencyDurationType(string $frequencyDurationType): void
    {
        $this->frequencyDurationType = $frequencyDurationType;
    }

    /**
     * @inheritdoc
     */
    public function isPrivate(): bool
    {
        return $this->private;
    }

    /**
     * @inheritdoc
     */
    public function setPrivate(bool $private): void
    {
        $this->private = $private;
    }

    /**
     * @inheritdoc
     */
    public function getPackage(): ?ApiPackageInterface
    {
        return $this->package;
    }

    /**
     * @inheritdoc
     */
    public function setPackage(ApiPackageInterface $package): void
    {
        $this->package = $package;
    }

    /**
     * @inheritdoc
     */
    public function isProrate(): bool
    {
        return $this->prorate;
    }

    /**
     * @inheritdoc
     */
    public function setProrate(bool $prorate): void
    {
        $this->prorate = $prorate;
    }

    /**
     * @inheritdoc
     */
    public function isPublished(): bool
    {
        return $this->published;
    }

    /**
     * @inheritdoc
     */
    public function setPublished(bool $published): void
    {
        $this->published = $published;
    }

    /**
     * @inheritdoc
     */
    public function getRatePlanDetails(): array
    {
        return $this->ratePlanDetails;
    }

    /**
     * @inheritdoc
     */
    public function setRatePlanDetails(array $ratePlanDetails): void
    {
        $this->ratePlanDetails = $ratePlanDetails;
    }

    /**
     * @inheritdoc
     */
    public function getRecurringFee(): ?float
    {
        return $this->recurringFee;
    }

    /**
     * @inheritdoc
     */
    public function setRecurringFee(float $recurringFee): void
    {
        $this->recurringFee = $recurringFee;
    }

    /**
     * @inheritdoc
     */
    public function getRecurringStartUnit(): ?int
    {
        return $this->recurringStartUnit;
    }

    /**
     * @inheritdoc
     */
    public function setRecurringStartUnit(int $recurringStartUnit): void
    {
        $this->recurringStartUnit = $recurringStartUnit;
    }

    /**
     * @inheritdoc
     */
    public function getRecurringType(): string
    {
        return $this->recurringType;
    }

    /**
     * @inheritdoc
     */
    public function setRecurringType(string $recurringType): void
    {
        $this->recurringType = $recurringType;
    }

    /**
     * @inheritdoc
     */
    public function getSetUpFee(): float
    {
        return $this->setUpFee;
    }

    /**
     * @inheritdoc
     */
    public function setSetUpFee(float $setUpFee): void
    {
        $this->setUpFee = $setUpFee;
    }
}
