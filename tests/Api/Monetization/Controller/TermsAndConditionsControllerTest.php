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

use Apigee\Edge\Api\Monetization\Controller\TermsAndConditionsController;
use Apigee\Edge\Api\Monetization\Entity\EntityInterface as MintEntityInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Api\Monetization\Entity\TermsAndConditionsEntityProviderTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTraitTest;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationTestControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityUpdateOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\MockClientAwareTrait;
use Apigee\Edge\Tests\Test\TestClientFactory;
use GuzzleHttp\Psr7\Response;

/**
 * Class TermsAndConditionsControllerTest.
 *
 * @group controller
 * @group monetization
 */
class TermsAndConditionsControllerTest extends EntityControllerTestBase
{
    use TermsAndConditionsEntityProviderTrait;
    use MockClientAwareTrait;
    // The order of these trait matters. Check @depends in test methods.
    use EntityCreateOperationControllerTraitTest;
    use EntityLoadOperationControllerTestTrait;
    use EntityUpdateOperationControllerTestTrait;
    use EntityDeleteOperationControllerTestTrait;
    use TimezoneConversionTestTrait;

    public function testGetEntities(): void
    {
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = static::mockApiClient()->getMockHttpClient();
        $httpClient->setDefaultResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) [[]])));
        /** @var \Apigee\Edge\Api\Monetization\Controller\TermsAndConditionsControllerInterface $controller */
        $controller = new TermsAndConditionsController(static::defaultTestOrganization(static::mockApiClient()), static::mockApiClient());
        $controller->getEntities();
        $this->assertEquals('all=true', static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getQuery());
        $controller->getEntities(true);
        $this->assertEquals('current=true&all=true', static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getQuery());
        $controller->getPaginatedEntityList();
        $this->assertEquals('page=1', static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getQuery());
        $controller->getPaginatedEntityList(1, 2, true);
        $this->assertEquals('current=true&page=2&size=1', static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getQuery());
        $controller->getPaginatedEntityList(2, 1, false);
        $this->assertEquals('current=false&page=1&size=2', static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getQuery());
    }

    /**
     * @inheritdoc
     */
    protected static function entityController(ClientInterface $client = null): EntityControllerTesterInterface
    {
        $client = $client ?? static::defaultAPIClient();

        return new EntityControllerTester(new TermsAndConditionsController(static::defaultTestOrganization($client), $client));
    }

    /**
     * @inheritdoc
     */
    protected static function entityCreateOperationTestController(): EntityCreateOperationTestControllerTesterInterface
    {
        return new EntityCreateOperationControllerTester(static::entityController());
    }

    /**
     * @inheritdoc
     */
    protected static function getNewEntity(): EntityInterface
    {
        return static::getNewTnC(!TestClientFactory::isOfflineClient(static::defaultAPIClient()));
    }

    /**
     * @inheritdoc
     */
    protected function entityForUpdateTest(EntityInterface $existing): EntityInterface
    {
        return static::getUpdatedTnC($existing, !TestClientFactory::isOfflineClient(static::defaultAPIClient()));
    }

    /**
     * @inheritdoc
     */
    protected function getTestEntityForTimezoneConversion(): MintEntityInterface
    {
        // This is fine for now.
        return static::controllerForEntityLoad()->load('phpunit');
    }
}
