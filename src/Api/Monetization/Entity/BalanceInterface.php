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

use Apigee\Edge\Api\Monetization\Entity\Property\CurrencyPropertyInterface;
use Apigee\Edge\Api\Monetization\Structure\PaymentTransaction;

/**
 * Interface BalanceInterface.
 *
 * Represents an either prepaid- or postpaid (credit) balance.
 */
interface BalanceInterface extends EntityInterface, CurrencyPropertyInterface
{
    /**
     * @return float
     */
    public function getAmount(): float;

    /**
     * @return bool
     */
    public function isChargePerUsage(): bool;

    /**
     * @return bool
     */
    public function isRecurring(): bool;

    /**
     * @return float
     */
    public function getUsage(): float;

    /**
     * @return \Apigee\Edge\Api\Monetization\Structure\PaymentTransaction|null
     */
    public function getTransaction(): ?PaymentTransaction;

    /**
     * @return string
     */
    public function getProviderId(): string;

    /**
     * @return float
     */
    public function getRecurringAmount(): float;

    /**
     * @return float
     */
    public function getReplenishAmount(): float;
}
