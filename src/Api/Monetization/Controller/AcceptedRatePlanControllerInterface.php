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

namespace Apigee\Edge\Api\Monetization\Controller;

use Apigee\Edge\Api\Monetization\Entity\AcceptedRatePlanInterface;
use Apigee\Edge\Api\Monetization\Entity\RatePlanInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use DateTimeImmutable;

/**
 * Interface AcceptedRatePlanControllerInterface.
 *
 * @see https://apidocs.apigee.com/monetize/apis/
 * @see https://docs.apigee.com/api-platform/monetization/subscribe-published-rate-plan-using-api
 * @see https://docs.apigee.com/api-platform/monetization/view-rate-plans#viewingrateplansusingtheapi-viewingallacceptedrateplansforadeveloperusingtheapi
 */
interface AcceptedRatePlanControllerInterface extends EntityControllerInterface,
    EntityLoadOperationControllerInterface
{
    /**
     * Gets all accepted rate plans.
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\AcceptedRatePlanInterface[]
     */
    public function getAllAcceptedRatePlans(): array;

    /**
     * Gets accepted rate plans in the provided range.
     *
     * @param int|null $limit
     * @param int $page
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\AcceptedRatePlanInterface[]
     */
    public function getPaginatedAcceptedRatePlanList(int $limit = null, int $page = 1): array;

    /**
     * Accepts a rate plan.
     *
     * @param \Apigee\Edge\Api\Monetization\Entity\RatePlanInterface $ratePlan
     *   The rate plan to be accepted.
     * @param \DateTimeImmutable $startDate
     *   Date when the rate plan starts.
     * @param \DateTimeImmutable|null $endDate
     *   Date when the rate plan ends.
     * @param int|null $quotaTarget
     *   (This property is valid for adjustable notification rate plans only.)
     *   Target number of transactions allowed for the app developer.
     * @param bool|null $suppressWarning
     *   Flag that specifies whether to suppress the error if the developer
     *   attempts to accept a rate plan that overlaps another accepted
     *   rate plan.
     * @param bool|null $waveTerminationCharge
     *   Flag that specifies whether termination fees are waved when an active
     *   rate plan is terminated as part of activating the new rate plan.
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\AcceptedRatePlanInterface
     */
    public function acceptRatePlan(RatePlanInterface $ratePlan, DateTimeImmutable $startDate, ?DateTimeImmutable $endDate = null, ?int $quotaTarget = null, ?bool $suppressWarning = null, ?bool $waveTerminationCharge = null): AcceptedRatePlanInterface;

    /**
     * Update a rate plan that has been accepted by a developer.
     *
     * @param \Apigee\Edge\Api\Monetization\Entity\AcceptedRatePlanInterface $acceptedRatePlan
     *   Previously accepted rate plan that should be modified.
     * @param bool|null $suppressWarning
     *   Flag that specifies whether to suppress the error if the developer
     *   attempts to accept a rate plan that overlaps another accepted
     *   rate plan.
     * @param bool|null $waveTerminationCharge
     *   Flag that specifies whether termination fees are waved when an active
     *   rate plan is terminated as part of activating the new rate plan.
     */
    public function updateSubscription(AcceptedRatePlanInterface $acceptedRatePlan, ?bool $suppressWarning = null, ?bool $waveTerminationCharge = null): void;
}
