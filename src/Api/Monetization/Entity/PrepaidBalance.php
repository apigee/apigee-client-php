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

class PrepaidBalance extends Entity implements PrepaidBalanceInterface
{
    use CurrencyPropertyAwareTrait;

    /**
     * The approx, tax rate on a legal entity can be null, this is not.
     *
     * @var int
     */
    protected $approxTaxRate;

    /** @var float */
    protected $currentBalance;

    /** @var float */
    protected $currentTotalBalance;

    /**
     * Value of "supportedCurrency" from the API response.
     *
     * @var \Apigee\Edge\Api\Monetization\Entity\SupportedCurrency
     */
    protected $currency;

    /** @var float */
    protected $currentUsage;

    /** @var string */
    protected $month;

    /** @var float */
    protected $previousBalance;

    /** @var float */
    protected $tax;

    /** @var float */
    protected $topUps;

    /** @var float */
    protected $usage;

    /** @var int */
    protected $year;

    /**
     * {@inheritdoc}
     */
    public function getApproxTaxRate(): int
    {
        return $this->approxTaxRate;
    }

    /**
     * @param int $approxTaxRate
     */
    public function setApproxTaxRate(int $approxTaxRate): void
    {
        $this->approxTaxRate = $approxTaxRate;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentBalance(): float
    {
        return $this->currentBalance;
    }

    /**
     * @param float $currentBalance
     */
    public function setCurrentBalance(float $currentBalance): void
    {
        $this->currentBalance = $currentBalance;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentTotalBalance(): float
    {
        return $this->currentTotalBalance;
    }

    /**
     * @param float $currentTotalBalance
     */
    public function setCurrentTotalBalance(float $currentTotalBalance): void
    {
        $this->currentTotalBalance = $currentTotalBalance;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentUsage(): float
    {
        return $this->currentUsage;
    }

    /**
     * @param float $currentUsage
     */
    public function setCurrentUsage(float $currentUsage): void
    {
        $this->currentUsage = $currentUsage;
    }

    /**
     * {@inheritdoc}
     */
    public function getMonth(): string
    {
        return $this->month;
    }

    /**
     * @param string $month
     */
    public function setMonth(string $month): void
    {
        $this->month = $month;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousBalance(): float
    {
        return $this->previousBalance;
    }

    /**
     * @param float $previousBalance
     */
    public function setPreviousBalance(float $previousBalance): void
    {
        $this->previousBalance = $previousBalance;
    }

    /**
     * {@inheritdoc}
     */
    public function getTax(): float
    {
        return $this->tax;
    }

    /**
     * @param float $tax
     */
    public function setTax(float $tax): void
    {
        $this->tax = $tax;
    }

    /**
     * {@inheritdoc}
     */
    public function getTopUps(): float
    {
        return $this->topUps;
    }

    /**
     * @param float $topUps
     */
    public function setTopUps(float $topUps): void
    {
        $this->topUps = $topUps;
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
     */
    public function setUsage(float $usage): void
    {
        $this->usage = $usage;
    }

    /**
     * {@inheritdoc}
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear(int $year): void
    {
        $this->year = $year;
    }
}
