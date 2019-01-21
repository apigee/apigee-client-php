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

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\Property\CompaniesPropertyInterface;
use Apigee\Edge\Entity\Property\DeveloperIdPropertyInterface;
use Apigee\Edge\Entity\Property\EmailPropertyInterface;
use Apigee\Edge\Entity\Property\OrganizationNamePropertyInterface;

/**
 * Interface DeveloperInterface.
 */
interface DeveloperInterface extends
    AppOwnerInterface,
    CompaniesPropertyInterface,
    DeveloperIdPropertyInterface,
    EmailPropertyInterface,
    OrganizationNamePropertyInterface
{
    /**
     * @return string[] Names of apps that this developer owns.
     */
    public function getApps(): array;

    /**
     * @param string $appName Name of app.
     *
     * @return bool
     */
    public function hasApp(string $appName): bool;

    /**
     * @return string[] Name of companies that this developer is a member.
     */
    public function getCompanies(): array;

    /**
     * @param string $companyName Name of company.
     *
     * @return bool
     */
    public function hasCompany(string $companyName): bool;

    /**
     * @return string|null UUID of the developer entity.
     */
    public function getDeveloperId(): ?string;

    /**
     * @return string|null
     */
    public function getUserName(): ?string;

    /**
     * @param string $userName
     */
    public function setUserName(string $userName): void;

    /**
     * @return string|null Email address of developer.
     */
    public function getEmail(): ?string;

    /**
     * @param string $email Email address of developer.
     */
    public function setEmail(string $email): void;

    /**
     * @return string|null
     */
    public function getFirstName(): ?string;

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void;

    /**
     * @return string|null
     */
    public function getLastName(): ?string;

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void;
}
