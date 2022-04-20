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

namespace Apigee\Edge\Tests\Api\ApigeeX\Controller;

use Apigee\Edge\Api\ApigeeX\Entity\RatePlanInterface;
use Apigee\Edge\Tests\Api\ApigeeX\EntitySerializer\AcceptedRatePlanSerializerValidator;
use Apigee\Edge\Tests\Test\Controller\MockClientAwareTrait;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerValidatorInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

abstract class AcceptedRatePlanControllerTestBase extends EntityControllerTestBase
{
    use MockClientAwareTrait;
    use EntityLoadOperationControllerTestTrait;

    public function testGetAcceptedRatePlans(): void
    {
        /** @var \Apigee\Edge\Api\ApigeeX\Controller\AcceptedRatePlanControllerInterface $controller */
        $controller = static::entityController();
        $ratePlans = $controller->getAllAcceptedRatePlans();
        $input = json_decode((string) static::defaultAPIClient()->getJournal()->getLastResponse()->getBody());
        $input = reset($input);
        $i = 0;
        // Ensure array of objects can be parsed properly.
        foreach ($ratePlans as $ratePlan) {
            $this->entitySerializerValidator()->validate($input[$i], $ratePlan);
            ++$i;
        }
    }

    public function testGetPaginatedAcceptedRatePlanList(): void
    {
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = static::mockApiClient()->getMockHttpClient();
        $httpClient->setDefaultResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) [[]])));
        /** @var \Apigee\Edge\Api\ApigeeX\Controller\AcceptedRatePlanControllerInterface $controller */
        $controller = static::entityController(static::mockApiClient());
        $controller->getPaginatedAcceptedRatePlanList();
        $this->assertEquals('page=1', static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getQuery());
        $controller->getPaginatedAcceptedRatePlanList(1, 2);
        $this->assertEquals('page=2&size=1', static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getQuery());
    }

    public function testAcceptRatePlan(): void
    {
        $httpClient = static::mockApiClient()->getMockHttpClient();
        /** @var \Apigee\Edge\Api\ApigeeX\Controller\AcceptedRatePlanControllerInterface $acceptedController */
        $acceptedController = static::entityController(static::mockApiClient());
        /** @var \Apigee\Edge\Api\ApigeeX\Entity\RatePlanInterface $ratePlan */
        $ratePlan = $this->getRatePlanToAccept();
        $response = $this->getAcceptRatePlanResponse();
        $httpClient->addResponse($response);
        /** @var \Apigee\Edge\Api\ApigeeX\Entity\AcceptedRatePlanInterface $acceptedRatePlan */
        $acceptedRatePlan = $acceptedController->acceptRatePlan($ratePlan);
        $payload = json_decode((string) static::mockApiClient()->getJournal()->getLastRequest()->getBody());
        // Make sure we do not send properties with null values.
        $this->assertObjectNotHasAttribute('endTime', $payload);
        $this->assertObjectNotHasAttribute('startTime', $payload);

        // Make sure the properties copied from the response to the created
        // object.
        $this->assertNotNull($acceptedRatePlan->id());

        $httpClient->addResponse($response);
        /* @var \Apigee\Edge\Api\ApigeeX\Entity\AcceptedRatePlanInterface $acceptedRatePlan */
        $acceptedController->acceptRatePlan($ratePlan);
        $payload = json_decode((string) static::mockApiClient()->getJournal()->getLastRequest()->getBody());
        $this->assertNotNull($payload->apiproduct);
    }

    /**
     * Returns a response that will be returned for acceptRatePlan().
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    abstract protected function getAcceptRatePlanResponse(): ResponseInterface;

    /**
     * @return \Apigee\Edge\Api\ApigeeX\Entity\RatePlanInterface
     */
    abstract protected function getRatePlanToAccept(): RatePlanInterface;

    /**
     * {@inheritdoc}
     */
    protected function entitySerializerValidator(): EntitySerializerValidatorInterface
    {
        static $validator;
        if (null === $validator) {
            $validator = new AcceptedRatePlanSerializerValidator($this->entitySerializer());
        }

        return $validator;
    }

    // TODO: Implement testCancelSubscription() method.
}
