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

namespace Apigee\Edge\Api\Monetization\Entity\Property;

use Apigee\Edge\Api\Monetization\Entity\OrganizationProfileInterface;

/**
 * Trait OrganizationPropertyAwareTrait.
 *
 * @see \Apigee\Edge\Api\Monetization\Entity\OrganizationProfileInterface
 */
trait OrganizationPropertyAwareTrait
{
    /**
     * It can be null when a new entity is created.
     *
     * @var \Apigee\Edge\Api\Monetization\Entity\OrganizationProfile|null
     */
    protected $organization;

    /**
     * @inheritdoc
     */
    public function getOrganization(): ?OrganizationProfileInterface
    {
        return $this->organization;
    }

    /**
     * @inheritdoc
     *
     * @internal You do not need to set the organization on the entity, the
     * controller will do that for you.
     */
    public function setOrganization(OrganizationProfileInterface $organization): void
    {
        $this->organization = $organization;
    }
}
