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

use Apigee\Edge\Api\Monetization\Entity\Balance;
use Apigee\Edge\Api\Monetization\Entity\BalanceInterface;
use Apigee\Edge\Api\Monetization\Entity\PrepaidBalance;
use Apigee\Edge\Api\Monetization\Entity\PrepaidBalanceInterface;
use Apigee\Edge\Api\Monetization\Serializer\BalanceSerializer;
use Apigee\Edge\Api\Monetization\Serializer\PrepaidBalanceSerializer;
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
        $balanceSerializer = $balanceSerializer ?? new BalanceSerializer();
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
    public function topUpBalance(float $amount, string $currencyCode): BalanceInterface
    {
        $response = $this->client->post(
            $this->getBaseEndpointUri(),
            (string) json_encode((object) [
                'amount' => $amount,
                'supportedCurrency' => ['id' => $currencyCode],
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
    public function getByCurrency(string $currencyCode): ?BalanceInterface
    {
        $result = $this->listEntities($this->getBaseEndpointUri()->withQuery(http_build_query(['currencyId' => $currencyCode])));

        return empty($result) ? null : reset($result);
    }

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
    public function setupRecurringPayments(string $currencyCode, string $paymentProviderId, float $replenishAmount, float $recurringAmount): BalanceInterface
    {
        $response = $this->client->post(
            $this->getBaseEndpointUri()->withPath("{$this->getBaseEndpointUri()->getPath()}/recurring-setup")->withQuery(http_build_query(['supportedCurrencyId' => $currencyCode])),
            (string) json_encode((object) [
                'providerId' => $paymentProviderId,
                'isRecurring' => 'true',
                'replenishAmount' => $replenishAmount,
                'recurringAmount' => $recurringAmount,
            ])
        );

        return $this->entitySerializer->deserialize(
            (string) $response->getBody(),
            $this->getEntityClass(),
            'json'
        );
    }

    /**
     * Deactivate recurring payments.
     *
     * @param string $currencyCode
     * @param string $paymentProviderId
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\BalanceInterface
     */
    public function disableRecurringPayments(string $currencyCode, string $paymentProviderId): BalanceInterface
    {
        $response = $this->client->post(
            $this->getBaseEndpointUri()->withPath("{$this->getBaseEndpointUri()->getPath()}/recurring-setup")->withQuery(http_build_query(['supportedCurrencyId' => $currencyCode])),
            (string) json_encode((object) [
                'providerId' => $paymentProviderId,
                'chargePerUsage' => 'true',
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
    public function getPrepaidBalance(\DateTimeImmutable $billingMonth): array
    {
        return $this->listPrepaidBalances($billingMonth);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrepaidBalanceByCurrency(string $currencyCode, \DateTimeImmutable $billingMonth): ?PrepaidBalanceInterface
    {
        $result = $this->listPrepaidBalances($billingMonth, $currencyCode);

        return empty($result) ? null : reset($result);
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        // Majority of API endpoints in this controller returns a Balance
        // and not a PrepaidBalance object so this is the reason why we are
        // using it as a default.
        return Balance::class;
    }

    /**
     * Returns the URI of the prepaid balances endpoint.
     *
     * We have to introduce this because it is not regular that an entity
     * has more than one listing endpoint so getBaseEntityEndpoint() was
     * enough until this time.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    abstract protected function getPrepaidBalanceEndpoint(): UriInterface;

    /**
     * Helper function which returns prepaid balances..
     *
     * @param \DateTimeImmutable $billingMonth
     * @param string|null $currencyCode
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\PrepaidBalanceInterface[]
     *
     * @psalm-suppress PossiblyNullArrayOffset - id() does not return null here.
     */
    private function listPrepaidBalances(\DateTimeImmutable $billingMonth, ?string $currencyCode = null): array
    {
        $query_params = [
            'billingMonth' => strtoupper($billingMonth->format('F')),
            'billingYear' => $billingMonth->format('Y'),
        ];
        if (null !== $currencyCode) {
            $query_params['supportedCurrencyId'] = $currencyCode;
        }

        $balances = [];
        foreach ($this->getRawList($this->getPrepaidBalanceEndpoint()->withQuery(http_build_query($query_params))) as $item) {
            /** @var \Apigee\Edge\Api\Monetization\Entity\PrepaidBalanceInterface $balance */
            $balance = $this->prepaidBalanceSerializer->denormalize($item, $this->prepaidBalanceClass);
            $balances[$balance->id()] = $balance;
        }

        return $balances;
    }
}
