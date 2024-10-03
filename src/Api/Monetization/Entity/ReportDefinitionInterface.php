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

namespace Apigee\Edge\Api\Monetization\Entity;

use Apigee\Edge\Api\Monetization\Entity\Property\OrganizationPropertyInterface;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\AbstractCriteria;
use Apigee\Edge\Entity\Property\DescriptionPropertyInterface;
use Apigee\Edge\Entity\Property\NamePropertyInterface;
use DateTimeImmutable;

interface ReportDefinitionInterface extends EntityInterface, NamePropertyInterface, DescriptionPropertyInterface, OrganizationPropertyInterface
{
    public const TYPE_BILLING = 'BILLING';
    public const TYPE_PREPAID_BALANCE = 'PREPAID_BALANCE';
    public const TYPE_REVENUE = 'REVENUE';

    /**
     * @param AbstractCriteria $criteria
     */
    public function setCriteria(AbstractCriteria $criteria): void;

    /**
     * It can be null when a new definition object is created.
     *
     * Also, the API endpoint does not return the "lastModified" property in the
     * response body until a definition has been modified..
     *
     * @return DateTimeImmutable|null
     */
    public function getLastModified(): ?DateTimeImmutable;

    /**
     * @return AbstractCriteria
     */
    public function getCriteria(): AbstractCriteria;
}
