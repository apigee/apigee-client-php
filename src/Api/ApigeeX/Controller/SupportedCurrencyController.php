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

use Apigee\Edge\Api\Monetization\Controller\OrganizationAwareEntityController;
use Apigee\Edge\Api\Monetization\Controller\PaginatedEntityListingControllerAwareTrait;
use Apigee\Edge\Api\Monetization\Entity\SupportedCurrency;
use Apigee\Edge\Api\Monetization\Serializer\SupportedCurrencySerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

class SupportedCurrencyController extends OrganizationAwareEntityController implements SupportedCurrencyControllerInterface
{
    use EntityListingControllerTrait;
    use PaginatedEntityListingControllerAwareTrait;
    use PaginatedListingHelperTrait;

    /**
     * SupportedCurrencyController constructor .
     *
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new SupportedCurrencySerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities(): array
    {
        $currencyjson = '[{
                            "description": "United States Dollars",
                            "displayName": "United States Dollars",
                            "id": "usd",
                            "minimumTopupAmount": 1,
                            "name": "USD",
                            "status": "ACTIVE",
                            "organization" : {"currency" : "USD"}
                        }]';
        $currencyarray = json_decode($currencyjson);

        return $this->getdefaultCurrency($currencyarray);
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return 'null';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return SupportedCurrency::class;
    }
}
