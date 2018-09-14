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

use Apigee\Edge\Controller\EntityControllerInterface;

/**
 * Interface ApiPackageControllerInterface.
 *
 * Api packages can not be updated this is reason why this does not extend
 * the EntityCrudOperationsControllerInterface.
 *
 * @see https://apidocs.apigee.com/monetize/apis/
 * @see https://docs.apigee.com/monetization/content/create-api-packages#getpackapi
 * @see https://docs.apigee.com/api-platform/monetization/create-api-packages#createpackage
 */
interface ApiPackageControllerInterface extends
    EntityControllerInterface,
    EntityCreateOperationInterface,
    EntityDeleteOperationControllerInterface,
    EntityLoadOperationControllerInterface,
    PaginatedEntityListingControllerInterface
{
    /**
     * Adds an aPI product to an API package.
     *
     * @param string $apiPackageId
     *   API package id.
     * @param string $productId
     *   API product id.
     *
     * @see https://docs.apigee.com/api-platform/monetization/create-api-packages#addprodtopackapi
     */
    public function addProduct(string $apiPackageId, string $productId): void;

    /**
     * Removes an API product from an API package.
     *
     * @param string $apiPackageId
     *   API package id.
     * @param string $productId
     *   API product id.
     */
    public function deleteProduct(string $apiPackageId, string $productId): void;

    /**
     * Gets all available API packages for a developer.
     *
     * @param string $developerId
     *   Id or email address of the developer.
     * @param bool $active
     *   Whether to show only API packages with active rate plans or not.
     * @param bool $allAvailable
     *   Whether to show all available packages or only packages with developer
     *   specific rate plans.
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\ApiPackageInterface[]
     *
     * @see https://apidocs.apigee.com/monetize/apis/get/organizations/%7Borg_name%7D/developers/%7Bdeveloper_id%7D/monetization-packages
     */
    public function getAvailableApiPackages(string $developerId, bool $active = false, bool $allAvailable = true): array;
}
