<?php

/**
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

use Apigee\Edge\Entity\CommonEntityPropertiesInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Entity\Property\AttributesPropertyInterface;
use Apigee\Edge\Entity\Property\DescriptionPropertyInterface;
use Apigee\Edge\Entity\Property\DisplayNamePropertyInterface;
use Apigee\Edge\Entity\Property\EnvironmentsPropertyInterface;
use Apigee\Edge\Entity\Property\NamePropertyInterface;
use Apigee\Edge\Entity\Property\ScopesPropertyInterface;

/**
 * Interface ApiProductInterface.
 */
interface ApiProductInterface extends
    EntityInterface,
    AttributesPropertyInterface,
    CommonEntityPropertiesInterface,
    DescriptionPropertyInterface,
    DisplayNamePropertyInterface,
    EnvironmentsPropertyInterface,
    NamePropertyInterface,
    ScopesPropertyInterface
{
    /**
     * @return string[]
     */
    public function getProxies(): array;

    /**
     * @param string[] $proxy
     */
    public function setProxies(array $proxy): void;

    /**
     * @return int|null
     */
    public function getQuota(): ?int;

    /**
     * @param int $quota
     */
    public function setQuota(int $quota);

    /**
     * @return int|null
     */
    public function getQuotaInterval(): ?int;

    /**
     * @param int $quotaInterval
     */
    public function setQuotaInterval(int $quotaInterval): void;

    /**
     * @return string
     */
    public function getQuotaTimeUnit(): ?string;

    /**
     * @param string $quotaTimeUnit
     */
    public function setQuotaTimeUnit(string $quotaTimeUnit): void;

    /**
     * @return string
     */
    public function getApprovalType(): ?string;

    /**
     * @param string $approvalType
     */
    public function setApprovalType(string $approvalType): void;

    /**
     * @return string[]
     */
    public function getApiResources(): array;

    /**
     * @param string[] $apiResources
     */
    public function setApiResources(array $apiResources): void;
}
