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

use Apigee\Edge\Api\Monetization\Builder\RatePlanRevisionBuilder;
use Apigee\Edge\Api\Monetization\Controller\RatePlanController;
use Apigee\Edge\Api\Monetization\Entity\CompanyRatePlanInterface;
use Apigee\Edge\Api\Monetization\Entity\DeveloperCategoryRatePlanInterface;
use Apigee\Edge\Api\Monetization\Entity\DeveloperRatePlanInterface;
use Apigee\Edge\Api\Monetization\Entity\RatePlanRevisionInterface;
use Apigee\Edge\Api\Monetization\Entity\StandardRatePlanInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\EntitySerializerValidatorInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\RatePlanSerializerValidator;
use Apigee\Edge\Tests\Test\HttpClient\FileSystemResponseFactory;
use Apigee\Edge\Tests\Test\MockClient;
use Apigee\Edge\Tests\Test\TestClientFactory;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class RatePlanControllerTest extends OrganizationAwareEntityControllerTestBase
{
    use EntityLoadControllerOperationTestTrait {
        testLoad as private traitTestLoad;
    }
    use TimezoneConversionTestTrait;

    /**
     * @inheritdoc
     */
    public function testLoad(): void
    {
        $ids = [
            // Standard rate plan.
            'standard' => [StandardRatePlanInterface::class],
            // Standard rate plan revision.
            'standard-rev' => [StandardRatePlanInterface::class, RatePlanRevisionInterface::class],
            // Developer rate plan.
            'developer' => [DeveloperRatePlanInterface::class],
            // Developer rate plan revision.
            'developer-rev' => [DeveloperRatePlanInterface::class, RatePlanRevisionInterface::class],
            // Company (developer) rate plan.
            'company' => [CompanyRatePlanInterface::class],
            // Company (developer) rate plan revision.
            'company-rev' => [CompanyRatePlanInterface::class, RatePlanRevisionInterface::class],
            // Developer category specific rate plan.
            'developer_category' => [DeveloperCategoryRatePlanInterface::class],
            // Developer category specific rate plan revision.
            'developer_category-rev' => [DeveloperCategoryRatePlanInterface::class, RatePlanRevisionInterface::class],
            // Rate plan with revenue share and rate card.
            // We do not expect any class here, because it is not a special
            // type of rate plans.
            'revshare_ratecard' => [],
        ];

        foreach ($ids as $id => $expectedClasses) {
            $entity = $this->loadTestEntity($id);
            foreach ($expectedClasses as $expectedClass) {
                $this->assertInstanceOf($expectedClass, $entity, $id);
            }
            $this->validateLoadedEntity($entity);
        }
    }

    public function testGetEntities(): void
    {
        /** @var \Apigee\Edge\Tests\Test\MockClient $client */
        $client = TestClientFactory::getClient(MockClient::class);
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = $client->getMockHttpClient();
        $httpClient->setDefaultResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) [[]])));
        $controller = new RatePlanController('phpunit', static::getOrganization(static::$client), $client);
        $controller->getEntities();
        $this->assertEmpty($client->getJournal()->getLastRequest()->getUri()->getQuery());
        $controller->getEntities(true, true, true);
        $this->assertEquals('current=true&showPrivate=true&standard=true', $client->getJournal()->getLastRequest()->getUri()->getQuery());
        $controller->getEntities(false, false, false);
        $this->assertEquals('current=false&showPrivate=false&standard=false', $client->getJournal()->getLastRequest()->getUri()->getQuery());
    }

    public function testCreateNewRevision(): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Entity\RatePlanInterface $rate_plan */
        // Create a new revision from a rate plan revision.
        $rate_plan = $this->loadTestEntity('standard-rev');
        $rate_plan->setStartDate(new \DateTimeImmutable('now'));
        /** @var \Apigee\Edge\Api\Monetization\Entity\RatePlanRevisionInterface $rate_plan_revision */
        $rate_plan_revision_start_date = new \DateTimeImmutable('tomorrow');
        $rate_plan_revision = RatePlanRevisionBuilder::buildRatePlanRevision($rate_plan, $rate_plan_revision_start_date);
        $this->assertNull($rate_plan_revision->id());
        $this->assertNull($rate_plan_revision->getEndDate());
        // We do not validate whether previous rate plan === rate plan, because
        // it is not always true, see the next assert.
        $this->assertEquals($rate_plan_revision->getPreviousRatePlanRevision()->id(), $rate_plan->id());
        // The parent rate plan is not a rate plan revision anymore.
        $this->assertNotInstanceOf(RatePlanRevisionInterface::class, $rate_plan_revision->getPreviousRatePlanRevision());
        $this->assertEquals($rate_plan_revision_start_date, $rate_plan_revision->getStartDate());
        $client = new MockClient();
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = $client->getMockHttpClient();
        // Read (the same) rate plan revision JSON object from the filesystem
        // and return it a response to the sent API call.
        $response = (new FileSystemResponseFactory())->createResponseForRequest(new Request('GET', 'v1/mint/organizations/phpunit/monetization-packages/phpunit/rate-plans/standard-rev'));
        $httpClient->addResponse($response);
        $controller = new RatePlanController('phpunit', static::getOrganization(static::$client), $client);
        $controller->createNewRevision($rate_plan_revision);
        // After we got back the same rate plan revision object that we started
        // the ID should be set again.
        $this->assertNotNull($rate_plan_revision->id());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Start date should not be earlier than parent rate plan's start date.
     */
    public function testCreateNewRevisionWithIncorrectStartDate(): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Entity\RatePlanInterface $rate_plan */
        $rate_plan = $this->loadTestEntity('standard');
        $rate_plan->setStartDate(new \DateTimeImmutable('now'));
        RatePlanRevisionBuilder::buildRatePlanRevision($rate_plan, new \DateTimeImmutable('yesterday'));
    }

    protected function getTestEntityId(): string
    {
        return 'standard';
    }

    /**
     * @inheritdoc
     */
    protected static function getEntitySerializerValidator(): EntitySerializerValidatorInterface
    {
        static $validator;
        if (null === $validator) {
            $validator = new RatePlanSerializerValidator(static::getEntitySerializer());
        }

        return $validator;
    }

    /**
     * @inheritdoc
     */
    protected static function getEntityController(): EntityControllerInterface
    {
        static $controller;
        if (null === $controller) {
            $controller = new RatePlanController('phpunit', static::getOrganization(static::$client), static::$client);
        }

        return $controller;
    }
}
