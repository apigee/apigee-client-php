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

use Apigee\Edge\Api\ApigeeX\Controller\RatePlanController;
use Apigee\Edge\Api\ApigeeX\Entity\StandardRatePlanInterface;
use Apigee\Edge\Api\Monetization\Entity\EntityInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Tests\Api\ApigeeX\EntitySerializer\RatePlanSerializerValidator;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityLoadOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\MockClientAwareTrait;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerValidatorInterface;
use GuzzleHttp\Psr7\Response;

/**
 * Class RatePlanControllerTest.
 *
 * @group controller
 * @group monetization
 */
class RatePlanControllerTest extends EntityControllerTestBase
{
    use MockClientAwareTrait;
    use EntityLoadOperationControllerTestTrait {
        testLoad as private traitTestLoad;
    }

    /**
     * {@inheritdoc}
     */
    public function testLoad(): void
    {
        $ids = [
            // Standard rate plan for ApigeeX.
            'standard' => [StandardRatePlanInterface::class],
        ];

        foreach ($ids as $id => $expectedClasses) {
            $entity = $this->controllerForEntityLoad()->load($id);
            foreach ($expectedClasses as $expectedClass) {
                $this->assertInstanceOf($expectedClass, $entity, $id);
            }
            $this->validateLoadedEntity($entity, $entity);
        }
    }

    public function testGetEntities(): void
    {
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = static::mockApiClient()->getMockHttpClient();
        $httpClient->setDefaultResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) [[]])));
        $controller = new RatePlanController('phpunit', static::defaultTestOrganization(static::mockApiClient()), static::mockApiClient());
        $controller->getEntities();
        $this->assertEquals('expand=true&state=PUBLISHED', static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getQuery());
    }

    /**
     * {@inheritdoc}
     */
    protected function entitySerializerValidator(): EntitySerializerValidatorInterface
    {
        static $validator;
        if (null === $validator) {
            $validator = new RatePlanSerializerValidator($this->entitySerializer());
        }

        return $validator;
    }

    /**
     * {@inheritdoc}
     */
    protected static function entityController(ClientInterface $client = null): EntityControllerTesterInterface
    {
        $client = $client ?? static::defaultAPIClient();

        return new EntityControllerTester(new RatePlanController('phpunit', static::defaultTestOrganization($client), $client));
    }

    protected function getTestEntityForTimezoneConversion(): EntityInterface
    {
        // This is fine for now.
        return static::controllerForEntityLoad()->load('standard');
    }
}
