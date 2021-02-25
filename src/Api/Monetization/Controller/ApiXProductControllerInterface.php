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

namespace Apigee\Edge\Api\Monetization\Controller;

use Apigee\Edge\Api\Monetization\Entity\XRatePlanInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Controller\EntityCreateOperationControllerInterface;
use Apigee\Edge\Controller\EntityLoadOperationControllerInterface;

/**
 * Interface ApiXProductControllerInterface.
 *
 * Api XProduct can not be updated this is reason why this does not extend
 * the EntityCrudOperationsControllerInterface.
 *
 * @see https://apidocs.apigee.com/monetize/apis/
 * @see https://docs.apigee.com/monetization/content/create-api-packages#getpackapi
 * @see https://docs.apigee.com/api-platform/monetization/create-api-packages#createpackage
 */
interface ApiXProductControllerInterface extends
    EntityControllerInterface,
    EntityCreateOperationControllerInterface,
    EntityDeleteOperationControllerInterface,
    EntityLoadOperationControllerInterface,
    PaginatedEntityListingControllerInterface
{

    /**
     * Gets all available API xproducts for a developer.
     *
     * @param string $developerId
     *   Id or email address of the developer.
     * @param bool $active
     *   Whether to show only API xproducts with active rate plans or not.
     * @param bool $allAvailable
     *   Whether to show all available xproducts or only xproducts with developer
     *   specific rate plans.
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\ApiXProductInterface[]
     *
     * @see https://apidocs.apigee.com/monetize/apis/get/organizations/%7Borg_name%7D/developers/%7Bdeveloper_id%7D/apiproducts
     */
    public function getAvailableApiXProductsByDeveloper(string $developerId, bool $active = false, bool $allAvailable = true): array;

    /**
     * Gets all available API packages for a company.
     *
     * @param string $company
     *   Name of a company.
     * @param bool $active
     *   Whether to show only API packages with active rate plans or not.
     * @param bool $allAvailable
     *   Whether to show all available packages or only packages with company
     *   specific rate plans.
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\ApiXProductInterface[]
     *
     * @see https://apidocs.apigee.com/monetize/apis/get/organizations/%7Borg_name%7D/companies/%7Bdeveloper_id%7D/apiproducts
     */
    public function getAvailableApiXProductsByCompany(string $company, bool $active = false, bool $allAvailable = true): array;
}
