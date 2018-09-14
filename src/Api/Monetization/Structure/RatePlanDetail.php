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

namespace Apigee\Edge\Api\Monetization\Structure;

use Apigee\Edge\Api\Monetization\Entity\Property\FreemiumPropertiesAwareTrait;
use Apigee\Edge\Api\Monetization\Entity\Property\FreemiumPropertiesInterface;
use Apigee\Edge\Api\Monetization\Entity\Property\PaymentDueDaysPropertyAwareTrait;
use Apigee\Edge\Api\Monetization\Entity\Property\PaymentDueDaysPropertyInterface;
use Apigee\Edge\Structure\BaseObject;

/**
 * Class RatePlanDetails.
 *
 * @see https://docs.apigee.com/api-platform/monetization/create-rate-plans#rateplandetails
 */
final class RatePlanDetail extends BaseObject implements
    FreemiumPropertiesInterface,
    PaymentDueDaysPropertyInterface
{
    use FreemiumPropertiesAwareTrait;
    use PaymentDueDaysPropertyAwareTrait;

    public const DURATION_DAY = 'DAY';

    public const DURATION_WEEK = 'WEEK';

    public const DURATION_MONTH = 'MONTH';

    public const DURATION_QUARTER = 'QUARTER';

    public const DURATION_YEAR = 'YEAR';

    public const METERING_TYPE_UNIT = 'UNIT';

    public const METERING_TYPE_VOLUME = 'VOLUME';

    public const METERING_TYPE_STAIR_STEP = 'STAIR_STEP';

    public const METERING_TYPE_DEV_SPECIFIC = 'DEV_SPECIFIC';

    public const RATING_PARAMETER = 'VOLUME';

    public const REVENUE_TYPE_GROSS = 'GROSS';

    public const REVENUE_TYPE_NET = 'NET';

    public const TYPE_REVSHARE = 'REVSHARE';

    public const TYPE_RATECARD = 'RATECARD';

    public const TYPE_REVSHARE_RATECARD = 'REVSHARE_RATECARD';

    public const TYPE_USAGE_TARGET = 'USAGE_TARGET';

    /** @var null|bool */
    private $aggregateFreemiumCounters;

    /** @var null|bool */
    private $aggregateStandardCounters;

    /** @var null|int */
    private $duration;

    /** @var null|string */
    private $durationType;

    /** @var string */
    private $meteringType;

    /** @var \Apigee\Edge\Api\Monetization\Structure\RatePlanRate[] */
    private $ratePlanRates = [];

    /** @var string */
    private $ratingParameter;

    /** @var null|string */
    private $ratingParameterUnit;

    /** @var null|string */
    private $revenueType;

    /** @var string */
    private $type;

    /**
     * @return bool|null
     */
    public function getAggregateFreemiumCounters(): ?bool
    {
        return $this->aggregateFreemiumCounters;
    }

    /**
     * @param bool $aggregateFreemiumCounters
     */
    public function setAggregateFreemiumCounters(bool $aggregateFreemiumCounters): void
    {
        $this->aggregateFreemiumCounters = $aggregateFreemiumCounters;
    }

    /**
     * @return bool|null
     */
    public function getAggregateStandardCounters(): ?bool
    {
        return $this->aggregateStandardCounters;
    }

    /**
     * @param bool $aggregateStandardCounters
     */
    public function setAggregateStandardCounters(bool $aggregateStandardCounters): void
    {
        $this->aggregateStandardCounters = $aggregateStandardCounters;
    }

    /**
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return null|string
     */
    public function getDurationType(): ?string
    {
        return $this->durationType;
    }

    /**
     * @param string $durationType
     */
    public function setDurationType(string $durationType): void
    {
        $this->durationType = $durationType;
    }

    /**
     * @return string
     */
    public function getMeteringType(): string
    {
        return $this->meteringType;
    }

    /**
     * @param string $meteringType
     */
    public function setMeteringType(string $meteringType): void
    {
        $this->meteringType = $meteringType;
    }

    /**
     * @return \Apigee\Edge\Api\Monetization\Structure\RatePlanRate[]
     */
    public function getRatePlanRates(): array
    {
        return $this->ratePlanRates;
    }

    /**
     * @param \Apigee\Edge\Api\Monetization\Structure\RatePlanRate[] $ratePlanRates
     */
    public function setRatePlanRates(array $ratePlanRates): void
    {
        $this->ratePlanRates = $ratePlanRates;
    }

    /**
     * @return string
     */
    public function getRatingParameter(): string
    {
        return $this->ratingParameter;
    }

    /**
     * @param string $ratingParameter
     */
    public function setRatingParameter(string $ratingParameter): void
    {
        $this->ratingParameter = $ratingParameter;
    }

    /**
     * @return null|string
     */
    public function getRatingParameterUnit(): ?string
    {
        return $this->ratingParameterUnit;
    }

    /**
     * @param string $ratingParameterUnit
     */
    public function setRatingParameterUnit(string $ratingParameterUnit): void
    {
        $this->ratingParameterUnit = $ratingParameterUnit;
    }

    /**
     * @return null|string
     */
    public function getRevenueType(): ?string
    {
        return $this->revenueType;
    }

    /**
     * @param string $revenueType
     */
    public function setRevenueType(string $revenueType): void
    {
        $this->revenueType = $revenueType;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
