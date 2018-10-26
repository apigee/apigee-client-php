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

use Apigee\Edge\Entity\Property\DisplayNamePropertyAwareTrait;
use Apigee\Edge\Entity\Property\NamePropertyAwareTrait;
use Apigee\Edge\Structure\AttributesProperty;

/**
 * Describes a Company entity.
 */
class Company extends AppOwner implements CompanyInterface
{
    use DisplayNamePropertyAwareTrait;
    use NamePropertyAwareTrait;

    /**
     * It is organization and not organizationName in the API response.
     *
     * This is the reason why it does not implement
     * OrganizationNamePropertyInterface. We also id not created a name
     * converter just to hide this small inconsistency.
     *
     * @var string|null
     */
    protected $organization;

    /**
     * Company constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->attributes = new AttributesProperty();
        parent::__construct($values);
    }

    /**
     * @inheritdoc
     */
    public function setOrganization(string $organization): void
    {
        $this->organization = $organization;
    }

    /**
     * @inheritdoc
     */
    public function getOrganization(): ?string
    {
        return $this->organization;
    }
}
