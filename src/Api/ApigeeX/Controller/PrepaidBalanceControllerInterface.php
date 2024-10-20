<?php

/*
 * Copyright 2021 Google LLC
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

namespace Apigee\Edge\Api\ApigeeX\Controller;

use Apigee\Edge\Api\ApigeeX\Entity\PrepaidBalanceInterface;
use Apigee\Edge\Api\Monetization\Controller\PaginatedEntityListingControllerInterface;
use Apigee\Edge\Controller\EntityControllerInterface;

interface PrepaidBalanceControllerInterface extends EntityControllerInterface, PaginatedEntityListingControllerInterface
{
    /**
     * @param int $amount
     * @param float $amountnano
     * @param string $currencyCode
     * @param string $transaction_id
     *
     * @return PrepaidBalanceInterface
     */
    public function topUpBalance($amount, $amountnano, string $currencyCode, string $transaction_id): PrepaidBalanceInterface;

    /**
     * Gets prepaid balances.
     *
     * @return \Apigee\Edge\Api\ApigeeX\Entity\PrepaidBalanceInterface[]
     */
    public function getPrepaidBalance(): array;
}
