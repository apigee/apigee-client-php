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

use Apigee\Edge\Api\Monetization\Entity\Property\EndDatePropertyAwareTrait;
use Apigee\Edge\Api\Monetization\Entity\Property\StartDatePropertyAwareTrait;
use DateTimeImmutable;

abstract class AcceptedRatePlan extends Entity implements AcceptedRatePlanInterface
{
    use EndDatePropertyAwareTrait;
    use StartDatePropertyAwareTrait;

    /** @var DateTimeImmutable|null */
    protected $created;

    /** @var int|null */
    protected $quotaTarget;

    /** @var RatePlanInterface */
    protected $ratePlan;

    /** @var DateTimeImmutable|null */
    protected $updated;

    /** @var DateTimeImmutable|null */
    protected $renewalDate;

    /** @var DateTimeImmutable|null */
    protected $nextCycleStartDate;

    /** @var DateTimeImmutable|null */
    protected $nextRecurringFeeDate;

    /** @var DateTimeImmutable|null */
    protected $prevRecurringFeeDate;

    /**
     * {@inheritdoc}
     */
    public function getUpdated(): ?DateTimeImmutable
    {
        return $this->updated;
    }

    /**
     * @param DateTimeImmutable $updated
     *
     * @internal
     */
    public function setUpdated(DateTimeImmutable $updated): void
    {
        $this->updated = $updated;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreated(): ?DateTimeImmutable
    {
        return $this->created;
    }

    /**
     * @param DateTimeImmutable $created
     *
     * @internal
     */
    public function setCreated(DateTimeImmutable $created): void
    {
        $this->created = $created;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuotaTarget(): ?int
    {
        return $this->quotaTarget;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuotaTarget(int $quotaTarget): void
    {
        $this->quotaTarget = $quotaTarget;
    }

    /**
     * {@inheritdoc}
     */
    public function getRatePlan(): RatePlanInterface
    {
        return $this->ratePlan;
    }

    /**
     * {@inheritdoc}
     */
    public function setRatePlan(RatePlanInterface $ratePlan): void
    {
        $this->ratePlan = $ratePlan;
    }

    /**
     * {@inheritdoc}
     */
    public function getRenewalDate(): ?DateTimeImmutable
    {
        return $this->renewalDate;
    }

    /**
     * @param DateTimeImmutable $renewalDate
     *
     * @internal
     */
    public function setRenewalDate(DateTimeImmutable $renewalDate): void
    {
        $this->renewalDate = $renewalDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextCycleStartDate(): ?DateTimeImmutable
    {
        return $this->nextCycleStartDate;
    }

    /**
     * @param DateTimeImmutable $nextCycleStartDate
     *
     * @internal
     */
    public function setNextCycleStartDate(DateTimeImmutable $nextCycleStartDate): void
    {
        $this->nextCycleStartDate = $nextCycleStartDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextRecurringFeeDate(): ?DateTimeImmutable
    {
        return $this->nextRecurringFeeDate;
    }

    /**
     * @param DateTimeImmutable $nextRecurringFeeDate
     *
     * @internal
     */
    public function setNextRecurringFeeDate(DateTimeImmutable $nextRecurringFeeDate): void
    {
        $this->nextRecurringFeeDate = $nextRecurringFeeDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrevRecurringFeeDate(): ?DateTimeImmutable
    {
        return $this->prevRecurringFeeDate;
    }

    /**
     * @param DateTimeImmutable $prevRecurringFeeDate
     *
     * @internal
     */
    public function setPrevRecurringFeeDate(DateTimeImmutable $prevRecurringFeeDate): void
    {
        $this->prevRecurringFeeDate = $prevRecurringFeeDate;
    }
}
