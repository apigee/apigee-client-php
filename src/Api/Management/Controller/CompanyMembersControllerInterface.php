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

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Structure\CompanyMembership;

/**
 * Interface CompanyDevelopersControllerInterface.
 *
 * @see https://apidocs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/companies/%7Bcompany_name%7D/developers
 */
interface CompanyMembersControllerInterface extends CompanyAwareControllerInterface
{
    /**
     * List all developers associated with a company.
     *
     * @return \Apigee\Edge\Api\Management\Structure\CompanyMembership
     *   Array of developers with their optional roles in the company.
     */
    public function getMembers(): CompanyMembership;

    /**
     * Set (add/update/remove) members of a company.
     *
     * You have to pass the complete list of developers otherwise you remove previously added developers from
     * a company.
     *
     * @param \Apigee\Edge\Api\Management\Structure\CompanyMembership $members
     *   Array of developers with their optional roles in the company.
     *
     * @return \Apigee\Edge\Api\Management\Structure\CompanyMembership
     *   Array of developers with their optional roles in the company.
     */
    public function setMembers(CompanyMembership $members): CompanyMembership;

    /**
     * Removes a developer from a company.
     *
     * @param string $email
     *   Email address of a developer.
     */
    public function removeMember(string $email): void;
}
