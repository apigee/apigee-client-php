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

use Apigee\Edge\Api\ApigeeX\Entity\RatePlanInterface;
use Apigee\Edge\Api\Monetization\Controller\EntityDeleteOperationControllerInterface;
use Apigee\Edge\Api\Monetization\Controller\PaginatedEntityListingControllerInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Controller\EntityCreateOperationControllerInterface;
use Apigee\Edge\Controller\EntityLoadOperationControllerInterface;

/**
 * Interface ApiProductControllerInterface.
 *
 * Api XProduct can not be updated this is reason why this does not extend
 * the EntityCrudOperationsControllerInterface.
 *
 * TODO: Add documenation link
 */
interface ApiProductControllerInterface extends
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
     * @return \Apigee\Edge\Api\ApigeeX\Entity\ApiProductInterface[]
     *
     * TODO: Add documenation link
     */
    public function getAvailableApixProductsByDeveloper(string $developerId, bool $active = false, bool $allAvailable = true): array;

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
     * @return \Apigee\Edge\Api\ApigeeX\Entity\ApiProductInterface[]
     *
     * TODO: Add documenation link
     */
    public function getAvailableApixProductsByCompany(string $company, bool $active = false, bool $allAvailable = true): array;

    /**
     * Returns eligible products by a developer.
     *
     * @param string $entityId
     *   Id of a developer.
     *
     * @return \Apigee\Edge\Api\ApigeeX\Entity\ApiProductInterface[]
     *   List of eligible API products.
     */
    public function getEligibleProductsByDeveloper(string $entityId): array;
}
