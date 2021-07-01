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

use Apigee\Edge\Api\Monetization\Entity\Property\CurrencyPropertyAwareTrait;
use Apigee\Edge\Api\Monetization\Structure\PaymentTransaction;

class Balance extends Entity implements BalanceInterface
{
    use CurrencyPropertyAwareTrait;

    /**
     * Value of "supportedCurrency" from the API response.
     *
     * @var \Apigee\Edge\Api\Monetization\Entity\SupportedCurrency
     */
    protected $currency;

    /** @var float */
    protected $amount;

    /** @var bool */
    protected $chargePerUsage;

    /**
     * Value of "isRecurring" from the API response.
     *
     * @var bool
     */
    protected $recurring;

    /** @var float */
    protected $usage;

    /** @var \Apigee\Edge\Api\Monetization\Structure\PaymentTransaction|null */
    protected $transaction;

    /**
     * {@inheritdoc}
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     *
     * @internal
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * {@inheritdoc}
     */
    public function isChargePerUsage(): bool
    {
        return $this->chargePerUsage;
    }

    /**
     * @param bool $chargePerUsage
     *
     * @internal
     */
    public function setChargePerUsage(bool $chargePerUsage): void
    {
        $this->chargePerUsage = $chargePerUsage;
    }

    /**
     * {@inheritdoc}
     */
    public function isRecurring(): bool
    {
        return $this->recurring;
    }

    /**
     * @param bool $recurring
     *
     * @internal
     */
    public function setRecurring(bool $recurring): void
    {
        $this->recurring = $recurring;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsage(): float
    {
        return $this->usage;
    }

    /**
     * @param float $usage
     *
     * @internal
     */
    public function setUsage(float $usage): void
    {
        $this->usage = $usage;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransaction(): ?PaymentTransaction
    {
        return $this->transaction;
    }

    /**
     * @param \Apigee\Edge\Api\Monetization\Structure\PaymentTransaction $transaction
     *
     * @internal
     */
    public function setTransaction(PaymentTransaction $transaction): void
    {
        $this->transaction = $transaction;
    }
}
