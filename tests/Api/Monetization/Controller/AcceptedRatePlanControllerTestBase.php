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

use Apigee\Edge\Api\Monetization\Controller\RatePlanController;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\AcceptedRatePlanSerializerValidator;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\EntitySerializerValidatorInterface;
use Apigee\Edge\Tests\Test\HttpClient\FileSystemResponseFactory;
use Apigee\Edge\Tests\Test\MockClient;
use Apigee\Edge\Tests\Test\TestClientFactory;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

abstract class AcceptedRatePlanControllerTestBase extends OrganizationAwareEntityControllerTestBase
{
    use EntityLoadControllerOperationTestTrait;

    public function testGetAcceptedRatePlans(): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Controller\AcceptedRatePlanControllerInterface $controller */
        $controller = $this->getEntityController();
        $ratePlans = $controller->getAllAcceptedRatePlans();
        $input = json_decode((string) static::$client->getJournal()->getLastResponse()->getBody());
        $input = reset($input);
        $i = 0;
        // Ensure array of objects can be parsed properly.
        foreach ($ratePlans as $ratePlan) {
            $this->getEntitySerializerValidator()->validate($input[$i], $ratePlan);
            ++$i;
        }
    }

    public function testGetPaginatedAcceptedRatePlanList(): void
    {
        /** @var \Apigee\Edge\Tests\Test\MockClient $client */
        $client = (new TestClientFactory())->getClient(MockClient::class);
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = $client->getMockHttpClient();
        $httpClient->setDefaultResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) [[]])));
        /** @var \Apigee\Edge\Api\Monetization\Controller\AcceptedRatePlanControllerInterface $controller */
        $controller = $this->getMockEntityController($client);
        $controller->getPaginatedAcceptedRatePlanList();
        $this->assertEquals('page=1', $client->getJournal()->getLastRequest()->getUri()->getQuery());
        $controller->getPaginatedAcceptedRatePlanList(1, 2);
        $this->assertEquals('page=2&size=1', $client->getJournal()->getLastRequest()->getUri()->getQuery());
    }

    public function testAcceptRatePlan(): void
    {
        $client = new MockClient();
        $httpClient = $client->getMockHttpClient();
        // Load a rate plan revision to make sure that works as well.
        $response = (new FileSystemResponseFactory())->createResponseForRequest(new Request('GET', 'v1/mint/organizations/phpunit/monetization-packages/phpunit/rate-plans/standard-rev'));
        $httpClient->addResponse($response);
        /** @var \Apigee\Edge\Api\Monetization\Controller\RatePlanControllerInterface $ratePlanController */
        $ratePlanController = new RatePlanController('phpunit', static::getOrganization(static::$client), $client);
        /** @var \Apigee\Edge\Api\Monetization\Controller\AcceptedRatePlanControllerInterface $acceptedController */
        $acceptedController = $this->getMockEntityController($client);
        /** @var \Apigee\Edge\Api\Monetization\Entity\RatePlanInterface $ratePlan */
        $ratePlan = $ratePlanController->load('phpunit');
        $startDate = new \DateTimeImmutable('now');
        $response = $this->getAcceptRatePlanResponse();
        $httpClient->addResponse($response);
        /** @var \Apigee\Edge\Api\Monetization\Entity\AcceptedRatePlanInterface $acceptedRatePlan */
        $acceptedRatePlan = $acceptedController->acceptRatePlan($ratePlan, $startDate);
        $payload = json_decode((string) $client->getJournal()->getLastRequest()->getBody());
        // Make sure we do not send properties with null values.
        $this->assertObjectNotHasAttribute('endDate', $payload);
        $this->assertObjectNotHasAttribute('quotaTarget', $payload);
        $this->assertObjectNotHasAttribute('suppressWarning', $payload);
        $this->assertObjectNotHasAttribute('waveTerminationCharge', $payload);
        // Make sure the properties copied from the response to the created
        // object.
        $this->assertNotNull($acceptedRatePlan->id());

        $httpClient->addResponse($response);
        /* @var \Apigee\Edge\Api\Monetization\Entity\AcceptedRatePlanInterface $acceptedRatePlan */
        $acceptedController->acceptRatePlan($ratePlan, $startDate, new \DateTimeImmutable('tomorrow'), 10, false, false);
        $payload = json_decode((string) $client->getJournal()->getLastRequest()->getBody());
        $this->assertNotNull($payload->endDate);
        $this->assertEquals(10, $payload->quotaTarget);
        $this->assertEquals('false', $payload->suppressWarning);
        $this->assertEquals('false', $payload->waveTerminationCharge);

        $httpClient->addResponse($response);
        /* @var \Apigee\Edge\Api\Monetization\Entity\AcceptedRatePlanInterface $acceptedRatePlan */
        $acceptedController->acceptRatePlan($ratePlan, $startDate, new \DateTimeImmutable('tomorrow'), 10, true, true);
        $payload = json_decode((string) $client->getJournal()->getLastRequest()->getBody());
        $this->assertEquals('true', $payload->suppressWarning);
        $this->assertEquals('true', $payload->waveTerminationCharge);
    }

    public function testUpdateSubscription(): void
    {
        $client = new MockClient();
        $httpClient = $client->getMockHttpClient();
        /** @var \Apigee\Edge\Api\Monetization\Controller\AcceptedRatePlanControllerInterface $acceptedController */
        $acceptedController = $this->getMockEntityController($client);
        $response = $this->getAcceptRatePlanResponse();
        // Response to the load().
        $httpClient->addResponse($response);
        // Response to updateSubscription().
        $httpClient->addResponse($response);
        /** @var \Apigee\Edge\Api\Monetization\Entity\AcceptedRatePlanInterface $acceptedRatePlan */
        $acceptedRatePlan = $acceptedController->load('phpunit');
        $originalStartDate = $acceptedRatePlan->getStartDate();
        $acceptedRatePlan->setStartDate(new \DateTimeImmutable('now'));
        $acceptedController->updateSubscription($acceptedRatePlan);
        $payload = json_decode((string) $client->getJournal()->getLastRequest()->getBody());
        // Make sure we do not send properties with null values.
        $this->assertObjectNotHasAttribute('suppressWarning', $payload);
        $this->assertObjectNotHasAttribute('waveTerminationCharge', $payload);
        // Make sure response values override values in the original object.
        $this->assertEquals($originalStartDate, $acceptedRatePlan->getStartDate());

        // Response to updateSubscription().
        $httpClient->addResponse($response);
        $acceptedController->updateSubscription($acceptedRatePlan, false, false);
        $payload = json_decode((string) $client->getJournal()->getLastRequest()->getBody());
        // Make sure we do not send properties with null values.
        $this->assertEquals('false', $payload->suppressWarning);
        $this->assertEquals('false', $payload->waveTerminationCharge);

        // Response to updateSubscription().
        $httpClient->addResponse($response);
        $acceptedController->updateSubscription($acceptedRatePlan, true, true);
        $payload = json_decode((string) $client->getJournal()->getLastRequest()->getBody());
        // Make sure we do not send properties with null values.
        $this->assertEquals('true', $payload->suppressWarning);
        $this->assertEquals('true', $payload->waveTerminationCharge);
    }

    /**
     * Returns a response that will be returned for acceptRatePlan().
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    abstract protected function getAcceptRatePlanResponse(): ResponseInterface;

    /**
     * @inheritdoc
     */
    protected static function getEntitySerializerValidator(): EntitySerializerValidatorInterface
    {
        static $validator;
        if (null === $validator) {
            $validator = new AcceptedRatePlanSerializerValidator(static::getEntitySerializer());
        }

        return $validator;
    }

    abstract protected static function getMockEntityController(ClientInterface $client): EntityControllerInterface;
}
