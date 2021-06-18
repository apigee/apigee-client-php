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
use Apigee\Edge\Entity\Property\DescriptionPropertyAwareTrait;
use Apigee\Edge\Entity\Property\NamePropertyAwareTrait;
use Apigee\Edge\Entity\Property\StatusPropertyAwareTrait;
use DateTimeZone;

class OrganizationProfile extends Entity implements OrganizationProfileInterface
{
    use AddressesPropertyAwareTrait;
    use BrokerPropertyAwareTrait;
    use DescriptionPropertyAwareTrait;
    use NamePropertyAwareTrait;
    use SelfBillingPropertyAwareTrait;
    use StatusPropertyAwareTrait;

    /** @var bool|null */
    protected $approveTrusted;

    /** @var bool|null */
    protected $approveUntrusted;

    /** @var string|null */
    protected $billingCycle;

    /** @var string|null */
    protected $country;

    /** @var string|null */
    protected $currencyCode;

    /** @var bool|null */
    protected $billingAdjustment;

    /** @var bool|null */
    protected $separateInvoiceForProduct;

    /** @var bool|null */
    protected $issueNettingStmt;

    /** @var string|null */
    protected $logoUrl;

    /** @var bool|null */
    protected $netPaymentAdviceNote;

    /** @var bool|null */
    protected $nettingStmtPerCurrency;

    /** @var string|null */
    protected $regNo;

    /** @var bool|null */
    protected $selfBillingAsExchOrg;

    /** @var bool */
    protected $selfBillingForAllDev;

    /** @var bool */
    protected $separateInvoiceForFees;

    /** @var string */
    protected $supportedBillingType;

    /** @var string|null */
    protected $taxEngineExternalId;

    /** @var string|null */
    protected $taxModel;

    /** @var string|null */
    protected $taxNexus;

    /** @var string|null */
    protected $taxRegNo;

    /** @var string|null */
    protected $transactionRelayURL;

    /** @var \DateTimeZone */
    protected $timezone;

    /**
     * {@inheritdoc}
     */
    public function getApproveTrusted(): ?bool
    {
        return $this->approveTrusted;
    }

    /**
     * {@inheritdoc}
     */
    public function isApproveTrusted(): bool
    {
        return (bool) $this->approveTrusted;
    }

    /**
     * {@inheritdoc}
     */
    public function setApproveTrusted(bool $approveTrusted): void
    {
        $this->approveTrusted = $approveTrusted;
    }

    /**
     * {@inheritdoc}
     */
    public function getApproveUntrusted(): ?bool
    {
        return $this->approveUntrusted;
    }

    /**
     * @return bool
     */
    public function isApproveUntrusted(): bool
    {
        return (bool) $this->approveUntrusted;
    }

    /**
     * {@inheritdoc}
     */
    public function setApproveUntrusted(bool $approveUntrusted): void
    {
        $this->approveUntrusted = $approveUntrusted;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingCycle(): ?string
    {
        return $this->billingCycle;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingCycle(string $billingCycle): void
    {
        $this->billingCycle = $billingCycle;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * {@inheritdoc}
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrencyCode(string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAdjustment(): ?bool
    {
        return $this->billingAdjustment;
    }

    /**
     * {@inheritdoc}
     */
    public function hasBillingAdjustment(): bool
    {
        return (bool) $this->billingAdjustment;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingAdjustment(bool $billingAdjustment): void
    {
        $this->billingAdjustment = $billingAdjustment;
    }

    /**
     * {@inheritdoc}
     */
    public function getSeparateInvoiceForProduct(): ?bool
    {
        return $this->separateInvoiceForProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function hasSeparateInvoiceForProduct(): bool
    {
        return (bool) $this->separateInvoiceForProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function setSeparateInvoiceForProduct(bool $separateInvoiceForProduct): void
    {
        $this->separateInvoiceForProduct = $separateInvoiceForProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function getIssueNettingStmt(): ?bool
    {
        return $this->issueNettingStmt;
    }

    /**
     * {@inheritdoc}
     */
    public function hasIssueNettingStmt(): bool
    {
        return (bool) $this->issueNettingStmt;
    }

    /**
     * {@inheritdoc}
     */
    public function setIssueNettingStmt(bool $issueNettingStmt): void
    {
        $this->issueNettingStmt = $issueNettingStmt;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogoUrl(string $logoUrl): void
    {
        $this->logoUrl = $logoUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getNetPaymentAdviceNote(): ?bool
    {
        return $this->netPaymentAdviceNote;
    }

    /**
     * {@inheritdoc}
     */
    public function hasNetPaymentAdviceNote(): bool
    {
        return (bool) $this->netPaymentAdviceNote;
    }

    /**
     * {@inheritdoc}
     */
    public function setNetPaymentAdviceNote(bool $netPaymentAdviceNote): void
    {
        $this->netPaymentAdviceNote = $netPaymentAdviceNote;
    }

    /**
     * {@inheritdoc}
     */
    public function getNettingStmtPerCurrency(): ?bool
    {
        return $this->nettingStmtPerCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function hasNettingStmtPerCurrency(): bool
    {
        return (bool) $this->nettingStmtPerCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function setNettingStmtPerCurrency(bool $nettingStmtPerCurrency): void
    {
        $this->nettingStmtPerCurrency = $nettingStmtPerCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegNo(): ?string
    {
        return $this->regNo;
    }

    /**
     * {@inheritdoc}
     */
    public function setRegNo(?string $regNo): void
    {
        $this->regNo = $regNo;
    }

    /**
     * {@inheritdoc}
     */
    public function getSelfBillingAsExchOrg(): ?bool
    {
        return $this->selfBillingAsExchOrg;
    }

    /**
     * {@inheritdoc}
     */
    public function isSelfBillingAsExchOrg(): bool
    {
        return (bool) $this->selfBillingAsExchOrg;
    }

    /**
     * {@inheritdoc}
     */
    public function setSelfBillingAsExchOrg(bool $selfBillingAsExchOrg): void
    {
        $this->selfBillingAsExchOrg = $selfBillingAsExchOrg;
    }

    /**
     * {@inheritdoc}
     */
    public function isSelfBillingForAllDev(): bool
    {
        return $this->selfBillingForAllDev;
    }

    /**
     * {@inheritdoc}
     */
    public function setSelfBillingForAllDev(bool $selfBillingForAllDev): void
    {
        $this->selfBillingForAllDev = $selfBillingForAllDev;
    }

    /**
     * {@inheritdoc}
     */
    public function isSeparateInvoiceForFees(): bool
    {
        return $this->separateInvoiceForFees;
    }

    /**
     * {@inheritdoc}
     */
    public function setSeparateInvoiceForFees(bool $separateInvoiceForFees): void
    {
        $this->separateInvoiceForFees = $separateInvoiceForFees;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedBillingType(): string
    {
        return $this->supportedBillingType;
    }

    /**
     * {@inheritdoc}
     */
    public function setSupportedBillingType(string $supportedBillingType): void
    {
        $this->supportedBillingType = $supportedBillingType;
    }

    /**
     * @return string|null
     */
    public function getTaxEngineExternalId(): ?string
    {
        return $this->taxEngineExternalId;
    }

    /**
     * @param string|null $taxEngineExternalId
     */
    public function setTaxEngineExternalId(?string $taxEngineExternalId): void
    {
        $this->taxEngineExternalId = $taxEngineExternalId;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxModel(): ?string
    {
        return $this->taxModel;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxModel(string $taxModel): void
    {
        $this->taxModel = $taxModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxNexus(): ?string
    {
        return $this->taxNexus;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxNexus(string $taxNexus): void
    {
        $this->taxNexus = $taxNexus;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRegNo(): ?string
    {
        return $this->taxRegNo;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxRegNo(string $taxRegNo): void
    {
        $this->taxRegNo = $taxRegNo;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionRelayURL(): ?string
    {
        return $this->transactionRelayURL;
    }

    /**
     * {@inheritdoc}
     */
    public function setTransactionRelayURL(string $transactionRelayURL): void
    {
        $this->transactionRelayURL = $transactionRelayURL;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimezone(): DateTimeZone
    {
        return $this->timezone;
    }

    /**
     * {@inheritdoc}
     */
    public function setTimezone(DateTimeZone $timezone): void
    {
        $this->timezone = $timezone;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * {@internal}
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
