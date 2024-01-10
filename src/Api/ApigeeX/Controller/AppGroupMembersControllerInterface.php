<?php

/*
 * Copyright 2023 Google LLC
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

use Apigee\Edge\Api\ApigeeX\Structure\AppGroupMembership;
use Apigee\Edge\Structure\AttributesProperty;

/**
 * Interface AppGroupMembersControllerInterface.
 */
interface AppGroupMembersControllerInterface extends AppGroupAwareControllerInterface
{
    /**
     * List all developers associated with a appgroup.
     *
     * @return AppGroupMembership
     *   Array of developers with their optional roles in the appgroup.
     */
    public function getMembers(): AppGroupMembership;

    /**
     * Set (add/update/remove) members of a appgroup.
     *
     * WARNING! If you pass en empty membership object you remove all developers
     * from the appgroup.
     *
     * @param AppGroupMembership $members
     *   Membership object with the changes to be applied.
     *
     * @return AppGroupMembership
     *   Membership object with the applied changes, it does not contain all
     *   members. Use getMembers() to retrieve them.
     */
    public function setMembers(AppGroupMembership $members);

    /**
     * Removes a developer from a appgroup.
     *
     * @param string $email
     *   Email address of a developer.
     */
    public function removeMember(string $email): void;
}
