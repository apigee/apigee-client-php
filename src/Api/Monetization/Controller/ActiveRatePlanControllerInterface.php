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

use Apigee\Edge\Api\Monetization\Entity\RatePlanInterface;
use Apigee\Edge\Controller\EntityControllerInterface;

/**
 * Interface ActiveRatePlanControllerInterface.
 *
 * @see https://apidocs.apigee.com/monetize/apis/
 * @see https://docs.apigee.com/api-platform/monetization/subscribe-published-rate-plan-using-api
 * @see https://docs.apigee.com/api-platform/monetization/view-rate-plans#viewingrateplansusingtheapi-viewingallactiverateplansforadeveloperusingtheapi
 */
interface ActiveRatePlanControllerInterface extends EntityControllerInterface,
    PaginatedEntityListingControllerInterface
{
    /**
     * Get active rate plan for a developer that contains an API product.
     *
     * @param string $apiProductName
     *   Name of the API product.
     * @param bool|null $showPrivate
     *   Flag that specifies whether to show a public or private plan (true) or public plan only (false). Defaults to false.
     *
     * @throws \Apigee\Edge\Exception\ApiResponseException
     *   If no rate plan found.
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\RatePlanInterface
     *   Rate plan.
     */
    public function getActiveRatePlanByApiProduct(string $apiProductName, ?bool $showPrivate = null): RatePlanInterface;
}
