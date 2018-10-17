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

namespace Apigee\Edge\Tests\Api\Monetization\Controller;

use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\EntitySerializerValidatorInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PrepaidBalanceSerializerValidator;
use Apigee\Edge\Tests\Test\MockClient;
use Apigee\Edge\Tests\Test\TestClientFactory;
use GuzzleHttp\Psr7\Response;

abstract class PrepaidBalanceControllerTestBase extends OrganizationAwareEntityControllerTestBase
{
    public function testGetEntities(): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Controller\PrepaidBalanceControllerInterface $controller */
        $controller = $this->getEntityController();
        /** @var \Apigee\Edge\Api\Monetization\Entity\BalanceInterface[] $entities */
        $entities = $controller->getEntities();
        $json = json_decode((string) static::getClient()->getJournal()->getLastResponse()->getBody());
        $json = reset($json);
        $i = 0;
        foreach ($entities as $entity) {
            $this->getEntitySerializerValidator()->validate($json[$i], $entity);
            ++$i;
        }
    }

    /**
     * testGetEntities() ensures that controller can parse instances of
     * BalanceInterface so we only validate the request here.
     */
    public function testMethodsThatReturnsBalance(): void
    {
        /** @var \Apigee\Edge\Tests\Test\MockClient $client */
        $client = TestClientFactory::getClient(MockClient::class);
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = $client->getMockHttpClient();
        $httpClient->setDefaultResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) [])));
        /** @var \Apigee\Edge\Api\Monetization\Controller\PrepaidBalanceControllerInterface $controller */
        $controller = $this->getMockEntityController($client);
        $currencyCode = 'USD';
        $paymentProviderId = 'example';
        $replenishAmount = 10;
        $recurringAmount = 10;
        $controller->setupRecurringPayments($currencyCode, $paymentProviderId, $replenishAmount, $recurringAmount);
        static::validateRecurringPath($client->getJournal()->getLastRequest()->getUri()->getPath());
        $this->assertEquals('supportedCurrencyId=USD', $client->getJournal()->getLastRequest()->getUri()->getQuery());
        $payload = json_decode((string) $client->getJournal()->getLastRequest()->getBody());
        // Ensure we validate all properties in the payload.
        $this->assertCount(4, (array) $payload);
        $this->assertEquals($paymentProviderId, $payload->providerId);
        $this->assertEquals('true', $payload->isRecurring);
        $this->assertEquals($replenishAmount, $payload->replenishAmount);
        $this->assertEquals($recurringAmount, $payload->recurringAmount);

        $controller->disableRecurringPayments($currencyCode, $paymentProviderId);
        $this->assertEquals('supportedCurrencyId=USD', $client->getJournal()->getLastRequest()->getUri()->getQuery());
        $payload = json_decode((string) $client->getJournal()->getLastRequest()->getBody());
        // Ensure we validate all properties in the payload.
        $this->assertCount(2, (array) $payload);
        $this->assertEquals($paymentProviderId, $payload->providerId);
        $this->assertEquals('true', $payload->chargePerUsage);

        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) [[]])));
        $controller->getByCurrency($currencyCode);
        $this->assertEquals('currencyId=USD', $client->getJournal()->getLastRequest()->getUri()->getQuery());

        $controller->topUpBalance($recurringAmount, $currencyCode);
        $payload = json_decode((string) $client->getJournal()->getLastRequest()->getBody());
        // Ensure we validate all properties in the payload.
        $this->assertCount(2, (array) $payload);
        $this->assertEquals($recurringAmount, $payload->amount);
        $this->assertEquals($currencyCode, $payload->supportedCurrency->id);
    }

    public function testGetPrepaidBalance(): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Controller\PrepaidBalanceControllerInterface $controller */
        $controller = $this->getEntityController();
        /** @var \Apigee\Edge\Api\Monetization\Entity\PrepaidBalanceInterface[] $entities */
        $entities = $controller->getPrepaidBalance(new \DateTimeImmutable('2018-10-01'));
        $json = json_decode((string) static::getClient()->getJournal()->getLastResponse()->getBody());
        $json = reset($json);
        $i = 0;
        // We need to prepaid balance serializer from the controller.
        $ro = new \ReflectionObject(static::getEntityController());
        $rp = $ro->getProperty('prepaidBalanceSerializer');
        $rp->setAccessible(true);
        $validator = new PrepaidBalanceSerializerValidator($rp->getValue(static::getEntityController()));
        foreach ($entities as $entity) {
            $validator->validate($json[$i], $entity);
            ++$i;
        }
    }

    /**
     * testGetPrepaidBalance() ensures that controller can parse instances of
     * PrepaidBalanceInterface so we only validate the request here.
     */
    public function testMethodsThatReturnsPrepaidBalance(): void
    {
        /** @var \Apigee\Edge\Tests\Test\MockClient $client */
        $client = TestClientFactory::getClient(MockClient::class);
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = $client->getMockHttpClient();
        $httpClient->setDefaultResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) [[]])));
        /** @var \Apigee\Edge\Api\Monetization\Controller\PrepaidBalanceControllerInterface $controller */
        $controller = $this->getMockEntityController($client);
        $currencyCode = 'USD';
        $billingMonth = new \DateTimeImmutable('now');
        $balance = $controller->getPrepaidBalanceByCurrency($currencyCode, $billingMonth);
        // In case of an empty result this should return null.
        $this->assertNull($balance);
        $expected = 'billingMonth=' . strtoupper($billingMonth->format('F'));
        $expected .= "&billingYear={$billingMonth->format('Y')}";
        $expected .= '&supportedCurrencyId=USD';
        $this->assertEquals($expected, $client->getJournal()->getLastRequest()->getUri()->getQuery());
    }

    abstract protected static function validateRecurringPath(string $actual): void;

    abstract protected static function getMockEntityController(ClientInterface $client): EntityControllerInterface;

    /**
     * @inheritdoc
     */
    protected static function getEntitySerializerValidator(): EntitySerializerValidatorInterface
    {
        static $validator;
        if (null === $validator) {
            $validator = new PrepaidBalanceSerializerValidator(static::getEntitySerializer());
        }

        return $validator;
    }
}
