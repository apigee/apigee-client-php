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

namespace Apigee\Edge\Api\Monetization\Controller;

use Apigee\Edge\Api\Monetization\Entity\BalanceInterface;
use Apigee\Edge\Api\Monetization\Entity\PrepaidBalanceInterface;
use Apigee\Edge\Controller\EntityControllerInterface;

interface PrepaidBalanceControllerInterface extends EntityControllerInterface
{
    /**
     * @param string $currencyCode
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\BalanceInterface|null
     */
    public function getByCurrency(string $currencyCode): ?BalanceInterface;

    /**
     * @param float $amount
     * @param string $currencyCode
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\BalanceInterface
     */
    public function topUpBalance(float $amount, string $currencyCode): BalanceInterface;

    /**
     * Enables and modifies recurring payment settings.
     *
     * @param string $currencyCode
     * @param string $paymentProviderId
     * @param float $replenishAmount
     * @param float $recurringAmount
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\BalanceInterface
     */
    public function setupRecurringPayments(string $currencyCode, string $paymentProviderId, float $replenishAmount, float $recurringAmount): BalanceInterface;

    /**
     * Deactivate recurring payments.
     *
     * @param string $currencyCode
     * @param string $paymentProviderId
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\BalanceInterface
     */
    public function disableRecurringPayments(string $currencyCode, string $paymentProviderId): BalanceInterface;

    /**
     * Gets prepaid balances.
     *
     * @param \DateTimeImmutable $billingMonth
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\PrepaidBalanceInterface[]
     */
    public function getPrepaidBalance(\DateTimeImmutable $billingMonth): array;

    /**
     * Gets prepaid balance by currency.
     *
     * @param string $currencyCode
     * @param \DateTimeImmutable $billingMonth
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\PrepaidBalanceInterface|null
     */
    public function getPrepaidBalanceByCurrency(string $currencyCode, \DateTimeImmutable $billingMonth): ?PrepaidBalanceInterface;
}
