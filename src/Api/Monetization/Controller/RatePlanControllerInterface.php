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

use Apigee\Edge\Api\Monetization\Entity\RatePlanRevisionInterface;
use Apigee\Edge\Controller\EntityControllerInterface;

/***
 * Interface RatePlanControllerInterface.
 *
 * @see https://apidocs.apigee.com/monetize/apis/
 * @see https://docs.apigee.com/api-platform/monetization/create-rate-plans
 * @see https://docs.apigee.com/api-platform/monetization/view-rate-plans
 */
interface RatePlanControllerInterface extends
    EntityControllerInterface,
    EntityCrudOperationsControllerInterface
{
    /**
     * List rate plans associated with an API package.
     *
     * Null as parameter value always falls back to the value documented
     * in the API documentation.
     *
     * @param bool|null $showCurrentOnly
     * @param bool $showPrivate
     * @param bool $showStandardOnly
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\RatePlanInterface[]
     *
     * @see https://apidocs.apigee.com/monetize/apis/get/organizations/%7Borg_name%7D/monetization-packages/%7Bpackage_id%7D/rate-plans
     */
    public function getEntities(?bool $showCurrentOnly = null, ?bool $showPrivate = null, ?bool $showStandardOnly = null): array;

    /**
     * Creates a new rate plan revision.
     *
     * @param \Apigee\Edge\Api\Monetization\Entity\RatePlanRevisionInterface $entity
     *   Rate plan revision to be created.
     */
    public function createNewRevision(RatePlanRevisionInterface $entity): void;
}
