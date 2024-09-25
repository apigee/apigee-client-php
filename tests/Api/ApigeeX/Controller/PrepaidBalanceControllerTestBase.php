<?php

/*
 * Copyright 2022 Google LLC
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

namespace Apigee\Edge\Tests\Api\ApigeeX\Controller;

use Apigee\Edge\Tests\Api\ApigeeX\EntitySerializer\PrepaidBalanceSerializerValidator;
use Apigee\Edge\Tests\Test\Controller\MockClientAwareTrait;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerValidatorInterface;
use GuzzleHttp\Psr7\Response;
use ReflectionObject;

/**
 * Base class for developer prepaid balance tests.
 */
abstract class PrepaidBalanceControllerTestBase extends EntityControllerTestBase
{
    use MockClientAwareTrait;

    public function testGetEntities(): void
    {
        /** @var \Apigee\Edge\Api\ApigeeX\Controller\PrepaidBalanceControllerInterface $controller */
        $controller = static::entityController();

        /** @var \Apigee\Edge\Api\ApigeeX\Entity\BalanceInterface[] $entities */
        $entities = $controller->getEntities();

        $json = json_decode((string) static::defaultAPIClient()->getJournal()->getLastResponse()->getBody());
        $json = reset($json);
        $i = 0;
        foreach ($entities as $entity) {
            // Calculate nanos before validating.
            $json[$i]->balance->nanos = ($json[$i]->balance->nanos ?? 0) * pow(10, -9);
            $this->entitySerializerValidator()->validate($json[$i], $entity);
            ++$i;
        }
    }

    /**
     * testGetEntities() ensures that controller can parse instances of
     * BalanceInterface so we only validate the request here.
     */
    public function testMethodsThatReturnsBalance(): void
    {
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = static::mockApiClient()->getMockHttpClient();
        $httpClient->setDefaultResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) [])));
        /** @var \Apigee\Edge\Api\ApigeeX\Controller\PrepaidBalanceControllerInterface $controller */
        $controller = static::entityController(static::mockApiClient());
        $payload = json_decode((string) static::mockApiClient()->getJournal()->getLastRequest()->getBody());

        // Ensure we validate all properties in the payload.
        $this->assertCount(1, (array) $payload);
        $transaction_id = '79943dfd-bb13-42ab-b8c9-04f5f73f6290-40';
        $amount = 10;
        $amountnano = 2000000;
        $currencyCode = 'USD';
        $controller->topUpBalance($amount, $amountnano, $currencyCode, $transaction_id);
        $payload = json_decode((string) static::mockApiClient()->getJournal()->getLastRequest()->getBody());
        // Ensure we validate all properties in the payload.
        $this->assertCount(2, (array) $payload);
        $this->assertEquals($amount, $payload->transactionAmount->units);
        $this->assertEquals($currencyCode, $payload->transactionAmount->currencyCode);
    }

    public function testGetPrepaidBalance(): void
    {
        /** @var \Apigee\Edge\Api\ApigeeX\Controller\PrepaidBalanceControllerInterface $controller */
        $controller = static::entityController();
        /** @var \Apigee\Edge\Api\ApigeeX\Entity\PrepaidBalanceInterface[] $entities */
        $entities = $controller->getPrepaidBalance();

        $json = json_decode((string) static::defaultAPIClient()->getJournal()->getLastResponse()->getBody());
        $json = reset($json);
        $i = 0;
        // We need to prepaid balance serializer from the controller.
        $ro = new ReflectionObject(static::entityController());
        $property = $ro->getProperty('decorated');
        $property->setAccessible(true);
        $ro = new ReflectionObject($property->getValue(static::entityController()));
        $rp = $ro->getProperty('prepaidBalanceSerializer');
        $rp->setAccessible(true);
        $validator = new PrepaidBalanceSerializerValidator($rp->getValue($property->getValue(static::entityController())));

        foreach ($entities as $entity) {
            // Calculate nanos before validating.
            $json[$i]->balance->nanos = ($json[$i]->balance->nanos ?? 0) * pow(10, -9);
            $validator->validate($json[$i], $entity);
            ++$i;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function entitySerializerValidator(): EntitySerializerValidatorInterface
    {
        static $validator;
        if (null === $validator) {
            $validator = new PrepaidBalanceSerializerValidator($this->entitySerializer());
        }

        return $validator;
    }
}
