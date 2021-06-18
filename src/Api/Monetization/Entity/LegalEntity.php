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
use Apigee\Edge\Entity\Property\AttributesPropertyAwareTrait;
use Apigee\Edge\Entity\Property\EmailPropertyAwareTrait;
use Apigee\Edge\Entity\Property\NamePropertyAwareTrait;
use Apigee\Edge\Entity\Property\StatusPropertyAwareTrait;
use Apigee\Edge\Structure\AttributesProperty;

/**
 * Parent class for developers and companies in Monetization.
 *
 * All setters marked as internal because the related Monetization endpoint only
 * supports GET. If you would like to modify developers or companies you should
 * use the related Management API controllers and entities.
 *
 * @see https://docs.apigee.com/api-platform/publish/adding-developers-your-api-product#definingmonetizationattributes
 *
 * Known issues:
 *  * Value of MINT_DEVELOPER_ADDRESS is not returned as "address" by the
 *  endpoint. When this gets fixed the value will be available here.
 */
abstract class LegalEntity extends OrganizationAwareEntity implements LegalEntityInterface
{
    // Because POST/PUT is not available in the Monetization API endpoints,
    // we simply just store the attributes in the same structure as in the
    // Management API integration and ignore "parent" and "id" properties.
    use AttributesPropertyAwareTrait;
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

    /** @var string|null */
    protected $legalName;

    /** @var string|null */
    protected $phone;

    /** @var string|null */
    protected $registrationId;

    /** @var DeveloperCategory|null */
    protected $developerCategory;

    /** @var float|null */
    protected $approxTaxRate;

    /** @var string|null */
    protected $taxExemptAuthNo;

    /**
     * LegalEntity constructor.
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
    public function getBillingType(): string
    {
        return $this->billingType;
    }

    /**
     * @param string $billingType
     *
     * {@internal}
     */
    public function setBillingType(string $billingType): void
    {
        $this->billingType = $billingType;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * {@internal}
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getLegalName(): ?string
    {
        return $this->legalName;
    }

    /**
     * @param string $legalName
     *
     * {@internal}
     */
    public function setLegalName(string $legalName): void
    {
        $this->legalName = $legalName;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @internal
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegistrationId(): ?string
    {
        return $this->registrationId;
    }

    /**
     * @param string $registrationId
     *
     * {@internal}
     */
    public function setRegistrationId(string $registrationId): void
    {
        $this->registrationId = $registrationId;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeveloperCategory(): ?DeveloperCategory
    {
        return $this->developerCategory;
    }

    /**
     * @param \Apigee\Edge\Api\Monetization\Entity\DeveloperCategory $developerCategory
     *
     * {@internal}
     */
    public function setDeveloperCategory(DeveloperCategory $developerCategory): void
    {
        $this->developerCategory = $developerCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function getApproxTaxRate(): ?float
    {
        return $this->approxTaxRate;
    }

    /**
     * @param float $approxTaxRate
     *
     * {@internal}
     */
    public function setApproxTaxRate(float $approxTaxRate): void
    {
        $this->approxTaxRate = $approxTaxRate;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxExemptAuthNo(): ?string
    {
        return $this->taxExemptAuthNo;
    }

    /**
     * @param string $taxExemptAuthNo
     *
     * {@internal}
     */
    public function setTaxExemptAuthNo(string $taxExemptAuthNo): void
    {
        $this->taxExemptAuthNo = $taxExemptAuthNo;
    }
}
