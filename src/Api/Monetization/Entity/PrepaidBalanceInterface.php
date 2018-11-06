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

/**
 * Interface PrepaidBalanceInterface.
 *
 * The related API endpoint is read-only so this interface only requires
 * getters.
 */
interface PrepaidBalanceInterface extends EntityInterface, CurrencyPropertyInterface
{
    /**
     * @return int
     */
    public function getApproxTaxRate(): int;

    /**
     * @return float
     */
    public function getCurrentBalance(): float;

    /**
     * @return float
     */
    public function getCurrentTotalBalance(): float;

    /**
     * @return float
     */
    public function getCurrentUsage(): float;

    /**
     * All-caps full text name of the month.
     *
     * @return string
     */
    public function getMonth(): string;

    /**
     * @return float
     */
    public function getPreviousBalance(): float;

    /**
     * @return float
     */
    public function getTax(): float;

    /**
     * @return float
     */
    public function getTopUps(): float;

    /**
     * @return float
     */
    public function getUsage(): float;

    /**
     * @return int
     */
    public function getYear(): int;
}
