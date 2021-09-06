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

use Apigee\Edge\Api\Monetization\Controller\EntityController;
use Apigee\Edge\Api\Monetization\Entity\SupportedCurrency;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Psr\Http\Message\UriInterface;

class SupportedCurrencyController extends EntityController implements SupportedCurrencyControllerInterface
{
    use EntityListingControllerTrait;

    /**
     * {@inheritdoc}
     */
    public function getEntities(): array
    {
        $currencyjson = '[{
                            "description": "United States Dollars",
                            "displayName": "United States Dollars",
                            "id": "usd",
                            "minimumTopupAmount": 1.00,
                            "name": "USD",
                            "status": "ACTIVE",
                            "organization" : {"currency" : "USD"}
                        }]';
        $currencyarray = json_decode($currencyjson);

        return $this->responseArrayToArrayOfEntities($currencyarray);
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        // ApigeeX does not provide api for supported currency , hence adding static value.
        return $this->client->getUriFactory()->createUri('');
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return SupportedCurrency::class;
    }
}
