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

use Apigee\Edge\Api\ApigeeX\Entity\PrepaidBalance;
use Apigee\Edge\Api\ApigeeX\Entity\PrepaidBalanceInterface;
use Apigee\Edge\Api\ApigeeX\Serializer\PrepaidBalanceSerializer;
use Apigee\Edge\Api\Monetization\Controller\OrganizationAwareEntityController;
use Apigee\Edge\Api\Monetization\Controller\PaginatedEntityListingControllerAwareTrait;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

abstract class PrepaidBalanceController extends OrganizationAwareEntityController implements PrepaidBalanceControllerInterface
{
    use PaginatedListingHelperTrait;
    use EntityListingControllerTrait;
    use PaginatedEntityListingControllerAwareTrait;

    /**
     * @var \Apigee\Edge\Serializer\EntitySerializerInterface
     */
    protected $prepaidBalanceSerializer;

    /**
     * The fully-qualified class name of the prepaid balances class.
     *
     * @var string
     */
    protected $prepaidBalanceClass;

    /**
     * PrepaidBalanceController constructor.
     *
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $balanceSerializer
     * @param string|null $prepaidBalanceClass
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $prepaidBalanceSerializer
     */
    public function __construct(string $organization, ClientInterface $client, ?EntitySerializerInterface $balanceSerializer = null, ?string $prepaidBalanceClass = null, ?EntitySerializerInterface $prepaidBalanceSerializer = null)
    {
        $balanceSerializer = $balanceSerializer ?? new PrepaidBalanceSerializer();
        // Majority of API endpoints in this controller returns a Balance
        // and not a PrepaidBalance object so this is the reason why we are
        // using it as the "default" serializer in this class.
        parent::__construct($organization, $client, $balanceSerializer);
        $this->prepaidBalanceSerializer = $prepaidBalanceSerializer ?? new PrepaidBalanceSerializer();
        $this->prepaidBalanceClass = $prepaidBalanceClass ?? PrepaidBalance::class;
    }

    /**
     * {@inheritdoc}
     */
    public function topUpBalance($amount, $amountnano, string $currencyCode, string $transaction_id): PrepaidBalanceInterface
    {
        $response = $this->client->post(
            $this->getBaseEndpointUri(),
            (string) json_encode((object) [
                'transactionAmount' => [
                  'currencyCode' => $currencyCode,
                  'units' => $amount,
                  'nanos' => $amountnano,
                ],
                'transactionId' => $transaction_id,
            ])
        );

        return $this->entitySerializer->deserialize(
            (string) $response->getBody(),
            $this->getEntityClass(),
            'json'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPrepaidBalance(): array
    {
        return $this->listPrepaidBalances();
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return PrepaidBalance::class;
    }

    /**
     * Returns the URI of the prepaid balances endpoint.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    abstract protected function getPrepaidBalanceEndpoint(): UriInterface;

    /**
     * Helper function which returns prepaid balances..
     *
     * @param string|null $currencyCode
     *
     * @return \Apigee\Edge\Api\ApigeeX\Entity\PrepaidBalanceInterface[]
     *
     * @psalm-suppress PossiblyNullArrayOffset - id() does not return null here.
     */
    private function listPrepaidBalances(?string $currencyCode = null): array
    {
        $balances = [];
        foreach ($this->getRawList($this->getPrepaidBalanceEndpoint()) as $item) {
            /** @var \Apigee\Edge\Api\ApigeeX\Entity\PrepaidBalanceInterface $balance */
            $balance = $this->prepaidBalanceSerializer->denormalize($item, $this->prepaidBalanceClass);
            $balances[$balance->getBalance()->getCurrencyCode()] = $balance;
        }

        return $balances;
    }
}
