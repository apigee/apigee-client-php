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

use Apigee\Edge\Api\Monetization\Entity\Property\VirtualCurrencyPropertyInterface;
use Apigee\Edge\Entity\Property\DescriptionPropertyInterface;
use Apigee\Edge\Entity\Property\DisplayNamePropertyInterface;
use Apigee\Edge\Entity\Property\NamePropertyInterface;
use Apigee\Edge\Entity\Property\StatusPropertyInterface;

interface SupportedCurrencyInterface extends OrganizationAwareEntityInterface,
    DescriptionPropertyInterface,
    DisplayNamePropertyInterface,
    NamePropertyInterface,
    StatusPropertyInterface,
    VirtualCurrencyPropertyInterface
{
    public const STATUS_ACTIVE = 'ACTIVE';

    public const STATUS_INACTIVE = 'INACTIVE';

    /**
     * Returns the credit limit.
     *
     * @return int|null
     */
    public function getCreditLimit(): ?int;

    /**
     * Sets the credit limit.
     *
     * @param int $creditLimit
     */
    public function setCreditLimit(int $creditLimit): void;

    /**
     * Indicates whether the currency is a virtual currency.
     *
     * @return bool
     */
    public function isVirtualCurrency(): bool;

    /**
     * Sets whether the supported currency is virtual currency.
     *
     * @param bool $virtualCurrency
     */
    public function setVirtualCurrency(bool $virtualCurrency): void;
}
