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

use Apigee\Edge\Api\Monetization\Entity\Property\AddressesPropertyInterface;
use Apigee\Edge\Api\Monetization\Entity\Property\BrokerPropertyInterface;
use Apigee\Edge\Api\Monetization\Entity\Property\SelfBillingPropertyInterface;
use Apigee\Edge\Entity\Property\AttributesPropertyInterface;
use Apigee\Edge\Entity\Property\EmailPropertyInterface;
use Apigee\Edge\Entity\Property\NamePropertyInterface;
use Apigee\Edge\Entity\Property\StatusPropertyInterface;

/**
 * Parent interface for developer and company entities in Monetization.
 */
interface LegalEntityInterface extends OrganizationAwareEntityInterface,
    AttributesPropertyInterface,
    AddressesPropertyInterface,
    BrokerPropertyInterface,
    EmailPropertyInterface,
    NamePropertyInterface,
    SelfBillingPropertyInterface,
    StatusPropertyInterface
{
    public const TYPE_TRUSTED = 'TRUSTED';

    public const TYPE_UNTRUSTED = 'UNTRUSTED';

    public const BILLING_TYPE_PREPAID = 'PREPAID';

    public const BILLING_TYPE_POSTPAID = 'POSTPAID';

    /**
     * @return string
     */
    public function getBillingType(): string;

    /**
     * @param string $billingType
     */
    public function setBillingType(string $billingType): void;

    /**
     * @return bool
     */
    public function isBroker(): bool;

    /**
     * @param bool $broker
     */
    public function setBroker(bool $broker): void;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     */
    public function setType(string $type): void;

    /**
     * @return null|string
     */
    public function getLegalName(): ?string;

    /**
     * @param string $legalName
     */
    public function setLegalName(string $legalName): void;

    /**
     * @return null|string
     */
    public function getPhone(): ?string;

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void;

    /**
     * @return null|string
     */
    public function getRegistrationId(): ?string;

    /**
     * @param string $registrationId
     */
    public function setRegistrationId(string $registrationId): void;

    /**
     * @return \Apigee\Edge\Api\Monetization\Entity\DeveloperCategory|null
     */
    public function getDeveloperCategory(): ?DeveloperCategory;

    /**
     * @param \Apigee\Edge\Api\Monetization\Entity\DeveloperCategory $developerCategory
     */
    public function setDeveloperCategory(DeveloperCategory $developerCategory): void;

    /**
     * @return float|null
     */
    public function getApproxTaxRate(): ?float;

    /**
     * @param float $approxTaxRate
     */
    public function setApproxTaxRate(float $approxTaxRate): void;

    /**
     * @return null|string
     */
    public function getTaxExemptAuthNo(): ?string;

    /**
     * @param string $taxExemptAuthNo
     */
    public function setTaxExemptAuthNo(string $taxExemptAuthNo): void;
}
