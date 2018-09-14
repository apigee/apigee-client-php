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
use Apigee\Edge\Entity\Property\DescriptionPropertyInterface;
use Apigee\Edge\Entity\Property\NamePropertyInterface;
use DateTimeZone;

interface OrganizationProfileInterface extends EntityInterface,
    AddressesPropertyInterface,
    BrokerPropertyInterface,
    DescriptionPropertyInterface,
    NamePropertyInterface,
    SelfBillingPropertyInterface
{
    public const BILLING_CYCLE_PRORATED = 'PRORATED';

    public const BILLING_CYCLE_CALENDAR_MONTH = 'CALENDAR_MONTH';

    public const STATUS_ACTIVE = 'ACTIVE';

    public const STATUS_INACTIVE = 'INACTIVE';

    public const BILLING_TYPE_PREPAID = 'PREPAID';

    public const BILLING_TYPE_POSTPAID = 'POSTPAID';

    public const BILLING_TYPE_BOTH = 'BOTH';

    public const TAX_MODEL_HYBRID = 'HYBRID';

    public const TAX_MODEL_DISCLOSED = 'DISCLOSED';

    public const TAX_MODEL_UNDISCLOSED = 'UNDISCLOSED';

    /**
     * @return bool|null
     */
    public function getApproveTrusted(): ?bool;

    /**
     * @param bool $approveTrusted
     */
    public function setApproveTrusted(bool $approveTrusted): void;

    /**
     * @return bool|null
     */
    public function getApproveUntrusted(): ?bool;

    /**
     * @param bool $approveUntrusted
     */
    public function setApproveUntrusted(bool $approveUntrusted): void;

    /**
     * @return null|string
     */
    public function getBillingCycle(): ?string;

    /**
     * @param string $billingCycle
     */
    public function setBillingCycle(string $billingCycle): void;

    /**
     * @return null|string
     */
    public function getCountry(): ?string;

    /**
     * @param string $country
     */
    public function setCountry(string $country): void;

    /**
     * @return null|string
     */
    public function getCurrencyCode(): ?string;

    /**
     * @param string $currency
     */
    public function setCurrencyCode(string $currency): void;

    /**
     * @return bool|null
     */
    public function getBillingAdjustment(): ?bool;

    /**
     * @param bool $hasBillingAdjustment
     */
    public function setBillingAdjustment(bool $hasBillingAdjustment): void;

    /**
     * @return bool|null
     */
    public function getSeparateInvoiceForProduct(): ?bool;

    /**
     * @param bool $hasSeparateInvoiceForProduct
     */
    public function setSeparateInvoiceForProduct(bool $hasSeparateInvoiceForProduct): void;

    /**
     * @return bool|null
     */
    public function getIssueNettingStmt(): ?bool;

    /**
     * @param bool $issueNettingStmt
     */
    public function setIssueNettingStmt(bool $issueNettingStmt): void;

    /**
     * @return null|string
     */
    public function getLogoUrl(): ?string;

    /**
     * @param string $logoUrl
     */
    public function setLogoUrl(string $logoUrl): void;

    /**
     * @return bool|null
     */
    public function getNetPaymentAdviceNote(): ?bool;

    /**
     * @param bool $netPaymentAdviceNote
     */
    public function setNetPaymentAdviceNote(bool $netPaymentAdviceNote): void;

    /**
     * @return null|bool
     */
    public function getNettingStmtPerCurrency(): ?bool;

    /**
     * @param bool $nettingStmtPerCurrency
     */
    public function setNettingStmtPerCurrency(bool $nettingStmtPerCurrency): void;

    /**
     * @return null|string
     */
    public function getRegNo(): ?string;

    /**
     * @param null|string $regNo
     */
    public function setRegNo(?string $regNo): void;

    /**
     * @return bool|null
     */
    public function getSelfBillingAsExchOrg(): ?bool;

    /**
     * @param bool $selfBillingAsExchOrg
     */
    public function setSelfBillingAsExchOrg(bool $selfBillingAsExchOrg): void;

    /**
     * @return bool
     */
    public function isSelfBillingForAllDev(): bool;

    /**
     * @param bool $selfBillingForAllDev
     */
    public function setSelfBillingForAllDev(bool $selfBillingForAllDev): void;

    /**
     * @return bool
     */
    public function isSeparateInvoiceForFees(): bool;

    /**
     * @param bool $separateInvoiceForFees
     */
    public function setSeparateInvoiceForFees(bool $separateInvoiceForFees): void;

    /**
     * @return string
     */
    public function getSupportedBillingType(): string;

    /**
     * @param string $supportedBillingType
     */
    public function setSupportedBillingType(string $supportedBillingType): void;

    /**
     * @return null|string
     */
    public function getTaxModel(): ?string;

    /**
     * @param string $taxModel
     */
    public function setTaxModel(string $taxModel): void;

    /**
     * @return null|string
     */
    public function getTaxNexus(): ?string;

    /**
     * @param string $taxNexus
     */
    public function setTaxNexus(string $taxNexus): void;

    /**
     * @return null|string
     */
    public function getTaxRegNo(): ?string;

    /**
     * @param string $taxRegNo
     */
    public function setTaxRegNo(string $taxRegNo): void;

    /**
     * @return null|string
     */
    public function getTransactionRelayURL(): ?string;

    /**
     * @param string $transactionRelayURL
     */
    public function setTransactionRelayURL(string $transactionRelayURL): void;

    /**
     * @return \DateTimeZone
     */
    public function getTimezone(): DateTimeZone;

    /**
     * @param \DateTimeZone $timezone
     */
    public function setTimezone(DateTimeZone $timezone): void;

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @return bool
     */
    public function isApproveTrusted(): bool;

    /**
     * @return bool
     */
    public function isApproveUntrusted(): bool;

    /**
     * @return bool
     */
    public function hasBillingAdjustment(): bool;

    /**
     * @return bool
     */
    public function hasSeparateInvoiceForProduct(): bool;

    /**
     * @return bool
     */
    public function hasIssueNettingStmt(): bool;

    /**
     * @return bool
     */
    public function hasNetPaymentAdviceNote(): bool;

    /**
     * @return bool
     */
    public function hasNettingStmtPerCurrency(): bool;

    /**
     * @return bool
     */
    public function isSelfBillingAsExchOrg(): bool;

    /**
     * @return null|string
     */
    public function getTaxEngineExternalId(): ?string;

    /**
     * @param null|string $taxEngineExternalId
     */
    public function setTaxEngineExternalId(?string $taxEngineExternalId): void;
}
