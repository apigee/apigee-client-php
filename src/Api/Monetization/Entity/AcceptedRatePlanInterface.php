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

use Apigee\Edge\Api\Monetization\Entity\Property\EndDatePropertyInterface;
use Apigee\Edge\Api\Monetization\Entity\Property\StartDatePropertyInterface;

interface AcceptedRatePlanInterface extends
    EntityInterface,
    EndDatePropertyInterface,
    StartDatePropertyInterface
{
    /**
     * @return \DateTimeImmutable|null
     */
    public function getUpdated(): ?\DateTimeImmutable;

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreated(): ?\DateTimeImmutable;

    /**
     * @return int|null
     */
    public function getQuotaTarget(): ?int;

    /**
     * @param int $quotaTarget
     */
    public function setQuotaTarget(int $quotaTarget): void;

    /**
     * @return \Apigee\Edge\Api\Monetization\Entity\RatePlanInterface
     */
    public function getRatePlan(): RatePlanInterface;

    /**
     * @param \Apigee\Edge\Api\Monetization\Entity\RatePlanInterface $ratePlan
     */
    public function setRatePlan(RatePlanInterface $ratePlan): void;

    /**
     * @return \DateTimeImmutable|null
     */
    public function getRenewalDate(): ?\DateTimeImmutable;

    /**
     * @return \DateTimeImmutable|null
     */
    public function getNextCycleStartDate(): ?\DateTimeImmutable;

    /**
     * @return \DateTimeImmutable|null
     */
    public function getNextRecurringFeeDate(): ?\DateTimeImmutable;

    /**
     * @return \DateTimeImmutable|null
     */
    public function getPrevRecurringFeeDate(): ?\DateTimeImmutable;
}
