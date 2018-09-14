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

namespace Apigee\Edge\Api\Monetization\Structure;

use Apigee\Edge\Api\Monetization\Entity\Property\DeveloperPropertyAwareTrait;
use Apigee\Edge\Api\Monetization\Entity\Property\DeveloperPropertyInterface;
use Apigee\Edge\Api\Monetization\Entity\Property\EndDatePropertyAwareTrait;
use Apigee\Edge\Api\Monetization\Entity\Property\EndDatePropertyInterface;
use Apigee\Edge\Api\Monetization\Entity\Property\IdPropertyAwareTrait;
use Apigee\Edge\Api\Monetization\Entity\Property\IdPropertyInterface;
use Apigee\Edge\Api\Monetization\Entity\Property\RatePropertyAwareTrait;
use Apigee\Edge\Api\Monetization\Entity\Property\RatePropertyInterface;
use Apigee\Edge\Api\Monetization\Entity\Property\StartDatePropertyAwareTrait;
use Apigee\Edge\Api\Monetization\Entity\Property\StartDatePropertyInterface;
use Apigee\Edge\Api\Monetization\Entity\Property\VirtualCurrencyPropertyAwareTrait;
use Apigee\Edge\Api\Monetization\Entity\Property\VirtualCurrencyPropertyInterface;
use Apigee\Edge\Entity\Property\StatusPropertyAwareTrait;
use Apigee\Edge\Entity\Property\StatusPropertyInterface;
use Apigee\Edge\Structure\BaseObject;

final class PaymentTransaction extends BaseObject implements
    IdPropertyInterface,
    DeveloperPropertyInterface,
    EndDatePropertyInterface,
    RatePropertyInterface,
    StartDatePropertyInterface,
    StatusPropertyInterface,
    VirtualCurrencyPropertyInterface
{
    use IdPropertyAwareTrait;
    use DeveloperPropertyAwareTrait;
    use EndDatePropertyAwareTrait;
    use RatePropertyAwareTrait;
    use StartDatePropertyAwareTrait;
    use StatusPropertyAwareTrait;
    use VirtualCurrencyPropertyAwareTrait;

    /**
     * Value of "endTime" from the API response.
     *
     * According to engineering, utcEndTime = endTime so we do not parse
     * the first one from the API response.
     *
     * TODO Can this be null?
     *
     * @var null|\DateTimeImmutable
     */
    protected $endDate;

    /**
     * Value of "startTime" from the API response,.
     *
     * According to engineering, utcStartTime = startTime so we do not parse
     * the first one from the API response.
     *
     * @var \DateTimeImmutable
     */
    protected $startDate;

    /**
     * Value of "isVirtualCurrency" from the API response.
     *
     * @var bool
     */
    protected $virtualCurrency;

    /** @var int */
    private $batchSize;

    /**
     * Value of "currency" from the API response.
     *
     * "currency" usually contains a currency object not jut a currency code.
     *
     * @var string
     */
    private $currencyCode;

    /** @var string */
    private $custAtt1;

    /** @var string */
    private $environment;

    /** @var string */
    private $notes;

    /** @var string */
    private $providerTxId;

    /** @var float */
    private $revenueShareAmount;

    /** @var string */
    private $txProviderStatus;

    /** @var string */
    private $type;

    /**
     * @return int
     */
    public function getBatchSize(): int
    {
        return $this->batchSize;
    }

    /**
     * @param int $batchSize
     */
    public function setBatchSize(int $batchSize): void
    {
        $this->batchSize = $batchSize;
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode(string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * @return string
     */
    public function getCustAtt1(): string
    {
        return $this->custAtt1;
    }

    /**
     * @param string $custAtt1
     */
    public function setCustAtt1(string $custAtt1): void
    {
        $this->custAtt1 = $custAtt1;
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @param string $environment
     */
    public function setEnvironment(string $environment): void
    {
        $this->environment = $environment;
    }

    /**
     * @return string
     */
    public function getNotes(): string
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     */
    public function setNotes(string $notes): void
    {
        $this->notes = $notes;
    }

    /**
     * @return string
     */
    public function getProviderTxId(): string
    {
        return $this->providerTxId;
    }

    /**
     * @param string $providerTxId
     */
    public function setProviderTxId(string $providerTxId): void
    {
        $this->providerTxId = $providerTxId;
    }

    /**
     * @return float
     */
    public function getRevenueShareAmount(): float
    {
        return $this->revenueShareAmount;
    }

    /**
     * @param float $revenueShareAmount
     */
    public function setRevenueShareAmount(float $revenueShareAmount): void
    {
        $this->revenueShareAmount = $revenueShareAmount;
    }

    /**
     * @return string
     */
    public function getTxProviderStatus(): string
    {
        return $this->txProviderStatus;
    }

    /**
     * @param string $txProviderStatus
     */
    public function setTxProviderStatus(string $txProviderStatus): void
    {
        $this->txProviderStatus = $txProviderStatus;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
