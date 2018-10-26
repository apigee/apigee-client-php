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
use Apigee\Edge\Api\Monetization\Entity\EntityInterface;
use Apigee\Edge\Api\Monetization\Entity\RatePlanRevisionInterface;
use Apigee\Edge\Api\Monetization\Entity\StandardRatePlanInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\RatePlanSerializerValidator;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\MockClientAwareTrait;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerValidatorInterface;
use Apigee\Edge\Tests\Test\HttpClient\FileSystemResponseFactory;
use GuzzleHttp\Psr7\Request;
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
        $this->assertEmpty(static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getQuery());
        $controller->getEntities(true, true, true);
        $this->assertEquals('current=true&showPrivate=true&standard=true', static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getQuery());
        $controller->getEntities(false, false, false);
        $this->assertEquals('current=false&showPrivate=false&standard=false', static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getQuery());
    }

    public function testCreateNewRevision(): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Entity\RatePlanInterface $rate_plan */
        // Create a new revision from a rate plan revision.
        $rate_plan = $this->controllerForEntityLoad()->load('standard-rev');
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
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = static::mockApiClient()->getMockHttpClient();
        // Read (the same) rate plan revision JSON object from the filesystem
        // and return it a response to the sent API call.
        $response = (new FileSystemResponseFactory())->createResponseForRequest(new Request('GET', 'v1/mint/organizations/phpunit/monetization-packages/phpunit/rate-plans/standard-rev'));
        $httpClient->addResponse($response);
        $controller = new RatePlanController('phpunit', static::defaultTestOrganization(static::defaultAPIClient()), static::mockApiClient());
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
        $rate_plan = $this->controllerForEntityLoad()->load('standard');
        $rate_plan->setStartDate(new \DateTimeImmutable('now'));
        RatePlanRevisionBuilder::buildRatePlanRevision($rate_plan, new \DateTimeImmutable('yesterday'));
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    protected static function entityController(): EntityControllerTesterInterface
    {
        return new EntityControllerTester(new RatePlanController('phpunit', static::defaultTestOrganization(static::defaultAPIClient()), static::defaultAPIClient()));
    }

    protected function getTestEntityForTimezoneConversion(): EntityInterface
    {
        // This is fine for now.
        return static::controllerForEntityLoad()->load('standard');
    }
}
