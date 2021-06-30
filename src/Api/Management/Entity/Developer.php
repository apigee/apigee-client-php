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

use Apigee\Edge\Entity\Property\DeveloperIdPropertyAwareTrait;
use Apigee\Edge\Entity\Property\EmailPropertyAwareTrait;
use Apigee\Edge\Entity\Property\OrganizationNamePropertyAwareTrait;
use Apigee\Edge\Structure\AttributesProperty;

/**
 * Describes a Developer entity.
 */
class Developer extends AppOwner implements DeveloperInterface
{
    use DeveloperIdPropertyAwareTrait;
    use EmailPropertyAwareTrait;
    use OrganizationNamePropertyAwareTrait;

    /** @var string */
    protected $userName;

    /** @var string */
    protected $firstName;

    /** @var string */
    protected $lastName;

    /** @var string[] */
    protected $companies = [];

    /**
    * The original email address of the developer.
    *
    * @var string|null
    */
    private $originalEmail;

    /**
     * Developer constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->attributes = new AttributesProperty();
        parent::__construct($values);
    }

    /**
     * {@inheritdoc}
     */
    public static function idProperty(): string
    {
        // The developerId is also a primary key of a developer entity. Other entities are usually
        // referencing to a developer with email address (except developer apps) but we should not use it
        // as a primary key, because email address can be changed and without a workaround it would cause problems when
        // \Apigee\Edge\Controller\BaseEntityControllerInterface::save() gets called.
        return 'developerId';
    }

    /**
     * {@inheritdoc}
     */
    public function getCompanies(): array
    {
        return $this->companies;
    }

    /**
     * Set company names from an Edge API response.
     *
     * Company memberships of a developer can not be changed by modifying this
     * property's value.
     *
     * @param string ...$companies
     *
     * @internal
     */
    public function setCompanies(string ...$companies): void
    {
        $this->companies = $companies;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCompany(string $companyName): bool
    {
        return in_array($companyName, $this->companies);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserName(): ?string
    {
        return $this->userName;
    }

    /**
     * {@inheritdoc}
     */
    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
        if (null === $this->originalEmail) {
            $this->originalEmail = $email;
        }
    }

    /**
     * The original email address of the developer used when updating user email address.
     *
     * @see Apigee\Edge\Api\Management\Controller\DeveloperController
     *
     * @internal 'get'is not prefixed to this method to prevent added to the request payload.
     */
    public function originalEmail(): ?string
    {
        return $this->originalEmail;
    }
}
