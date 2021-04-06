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

namespace Apigee\Edge\Api\ApigeeX\Controller;

use Apigee\Edge\Api\ApigeeX\Entity\AcceptedRatePlanInterface;
use Apigee\Edge\Api\ApigeeX\Entity\RatePlanInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Controller\EntityLoadOperationControllerInterface;

/**
 * Interface AcceptedRatePlanControllerInterface.
 *
 * TODO: Add reference documentation link
 */
interface AcceptedRatePlanControllerInterface extends
    EntityControllerInterface,
    EntityLoadOperationControllerInterface
{
    /**
     * Gets all accepted rate plans.
     *
     * @return \Apigee\Edge\Api\ApigeeX\Entity\AcceptedRatePlanInterface[]
     */
    public function getAllAcceptedRatePlans(): array;

    /**
     * Gets accepted rate plans in the provided range.
     *
     * @param int|null $limit
     * @param int $page
     *
     * @return \Apigee\Edge\Api\ApigeeX\Entity\AcceptedRatePlanInterface[]
     */
    public function getPaginatedAcceptedRatePlanList(int $limit = null, int $page = 1): array;

    /**
     * Accepts a rate plan.
     *
     * @param \Apigee\Edge\Api\ApigeeX\Entity\RatePlanInterface $ratePlan
     *   The rate plan to be accepted.
     *
     * @return \Apigee\Edge\Api\ApigeeX\Entity\AcceptedRatePlanInterface
     */
    public function acceptRatePlan(RatePlanInterface $ratePlan): AcceptedRatePlanInterface;

    /**
     * Update a rate plan that has been accepted by a developer.
     *
     * @param \Apigee\Edge\Api\ApigeeX\Entity\AcceptedRatePlanInterface $acceptedRatePlan
     *   Previously accepted rate plan that should be modified.
     */
    public function updateSubscription(AcceptedRatePlanInterface $acceptedRatePlan): void;
}
