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

use Apigee\Edge\Api\Monetization\Entity\Property\AddressesPropertyAwareTrait;
use Apigee\Edge\Api\Monetization\Entity\Property\BrokerPropertyAwareTrait;
use Apigee\Edge\Api\Monetization\Entity\Property\SelfBillingPropertyAwareTrait;
use Apigee\Edge\Api\Monetization\Structure\Address;
use Apigee\Edge\Entity\Property\EmailPropertyAwareTrait;
use Apigee\Edge\Entity\Property\NamePropertyAwareTrait;
use Apigee\Edge\Entity\Property\StatusPropertyAwareTrait;

/**
 * Parent class for developers and companies in Monetization.
 *
 * @see https://apidocs.apigee.com/management/apis/put/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D
 *
 * Known issues:
 *  * Value of MINT_DEVELOPER_ADDRESS is not returned as "address" by the
 *  endpoint. When this gets fixed the value will be available here.
 */
abstract class LegalEntity extends OrganizationAwareEntity implements LegalEntityInterface
{
    use AddressesPropertyAwareTrait;
    use BrokerPropertyAwareTrait;
    use EmailPropertyAwareTrait;
    use NamePropertyAwareTrait;
    use SelfBillingPropertyAwareTrait;
    use StatusPropertyAwareTrait;

    /** @var string */
    protected $billingType;

    /** @var string */
    protected $type;

    /** @var null|string */
    protected $legalName;

    /** @var null|string */
    protected $phone;

    /** @var null|string */
    protected $registrationId;

    /** @var null|DeveloperCategory */
    protected $developerCategory;

    /** @var null|float */
    protected $approxTaxRate;

    /** @var null|string */
    protected $taxExemptAuthNo;

    /**
     * @inheritdoc
     */
    public function getBillingType(): string
    {
        return $this->billingType;
    }

    /**
     * @inheritdoc
     */
    public function setBillingType(string $billingType): void
    {
        $this->billingType = $billingType;
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @inheritdoc
     */
    public function getLegalName(): ?string
    {
        return $this->legalName;
    }

    /**
     * @inheritdoc
     */
    public function setLegalName(string $legalName): void
    {
        $this->legalName = $legalName;
    }

    /**
     * @inheritdoc
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @inheritdoc
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationId(): ?string
    {
        return $this->registrationId;
    }

    /**
     * @inheritdoc
     */
    public function setRegistrationId(string $registrationId): void
    {
        $this->registrationId = $registrationId;
    }

    /**
     * @inheritdoc
     */
    public function getDeveloperCategory(): ?DeveloperCategory
    {
        return $this->developerCategory;
    }

    /**
     * @inheritdoc
     */
    public function setDeveloperCategory(DeveloperCategory $developerCategory): void
    {
        $this->developerCategory = $developerCategory;
    }

    /**
     * @inheritdoc
     */
    public function getApproxTaxRate(): ?float
    {
        return $this->approxTaxRate;
    }

    /**
     * @inheritdoc
     */
    public function setApproxTaxRate(float $approxTaxRate): void
    {
        $this->approxTaxRate = $approxTaxRate;
    }

    /**
     * @inheritdoc
     */
    public function getTaxExemptAuthNo(): ?string
    {
        return $this->taxExemptAuthNo;
    }

    /**
     * @inheritdoc
     */
    public function setTaxExemptAuthNo(string $taxExemptAuthNo): void
    {
        $this->taxExemptAuthNo = $taxExemptAuthNo;
    }
}
