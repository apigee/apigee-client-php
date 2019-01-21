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

namespace Apigee\Edge\Api\Management\Structure;

use Apigee\Edge\Structure\BaseObject;

/**
 * Contains members of a company.
 */
final class CompanyMembership extends BaseObject
{
    /**
     * An associate array where developer email addresses are the keys and developer roles are the values.
     *
     * The value can be null if a developer has no role in a company.
     *
     * @var array
     */
    private $members;

    /**
     * CompanyMembership constructor.
     *
     * @param array $members
     */
    public function __construct(array $members = [])
    {
        $this->members = $members;
        parent::__construct();
    }

    /**
     * Returns developers with their roles.
     *
     * @return array
     *  An associate array where developer email addresses are the keys and developer roles are the values.
     *  The value can be null if a developer has no role in a company.
     */
    public function getMembers(): array
    {
        return $this->members;
    }

    /**
     * Add developer or modify developer role in a membership.
     *
     * @param string $email
     *   Developer email address.
     * @param string|null $role
     *   Developer role.
     *
     * @return array
     *   An associate array where developer email addresses are the keys and developer roles are the values.
     *   The value can be null if a developer has no role in a company.
     */
    public function setMember(string $email, ?string $role = null): array
    {
        $this->members[$email] = $role;

        return $this->members;
    }

    /**
     * Removes a developer from the membership.
     *
     * @param string $email
     *   Developer email address.
     *
     * @return array
     *   An associate array where developer email addresses are the keys and developer roles are the values.
     *   The value can be null if a developer has no role in a company.
     */
    public function removeMember(string $email): array
    {
        unset($this->members[$email]);

        return $this->members;
    }

    /**
     * Returns whether a developer is a member of a company.
     *
     * @param string $email
     *   Developer email address.
     *
     * @return bool
     */
    public function isMember(string $email): bool
    {
        return array_key_exists($email, $this->members);
    }

    /**
     * Returns the role of a developer in a company from a membership.
     *
     * @param string $email
     *   Developer email address.
     *
     * @return string|null
     *   Developer role if set, NULL otherwise.
     */
    public function getRole(string $email): ?string
    {
        return $this->members[$email] ?? null;
    }
}
