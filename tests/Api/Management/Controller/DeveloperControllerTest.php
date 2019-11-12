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

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\Developer;
use Apigee\Edge\Api\Management\Entity\DeveloperInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Tests\Api\Management\Entity\DeveloperTestEntityProviderTrait;
use Apigee\Edge\Tests\Api\Management\Entity\ParameterUrlEncodingTestTrait;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationTestControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityDeleteOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityLoadOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityUpdateOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\MockClientAwareTrait;
use Apigee\Edge\Tests\Test\TestClientFactory;
use Apigee\Edge\Tests\Test\Utility\MarkOnlineTestSkippedAwareTrait;
use GuzzleHttp\Psr7\Response;

/**
 * Class DeveloperControllerTest.
 *
 * @group controller
 * @group management
 */
class DeveloperControllerTest extends EntityControllerTestBase
{
    use DeveloperControllerAwareTestTrait;
    use DeveloperTestEntityProviderTrait;
    use DefaultAPIClientAwareTrait;
    use MarkOnlineTestSkippedAwareTrait;
    use MockClientAwareTrait;
    use ParameterUrlEncodingTestTrait;
    // The order of these trait matters. Check @depends in test methods.
    use EntityCreateOperationControllerTestTrait;
    use EntityLoadOperationControllerTestTrait;
    use EntityUpdateOperationControllerTestTrait;
    use EntityDeleteOperationControllerTestTrait;
    use PaginatedEntityListingControllerTestTraitBase;
    use PaginatedEntityIdListingControllerTestTrait;
    use PaginatedEntityListingControllerTestTrait;
    use AttributesAwareEntityControllerTestTrait;

    /**
     * @group online
     *
     * @expectedException \Apigee\Edge\Exception\ClientErrorException
     */
    public function testCreateWithIncorrectData(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $entity = new Developer(['email' => 'developer-create-exception@example.com']);
        static::entityCreateOperationTestController()->create($entity);
    }

    /**
     * @group online
     */
    public function testStatusChange(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        /** @var \Apigee\Edge\Api\Management\Controller\DeveloperControllerInterface $controller */
        $controller = static::entityController();
        /** @var \Apigee\Edge\Api\Management\Entity\DeveloperInterface $entity */
        $entity = static::getNewEntity();
        $controller->create($entity);
        $controller->setStatus($entity->id(), DeveloperInterface::STATUS_INACTIVE);
        $entity = $controller->load($entity->id());
        $this->assertEquals($entity->getStatus(), DeveloperInterface::STATUS_INACTIVE);
        $controller->setStatus($entity->id(), DeveloperInterface::STATUS_ACTIVE);
        $entity = $controller->load($entity->id());
        $this->assertEquals($entity->getStatus(), DeveloperInterface::STATUS_ACTIVE);
    }

    /**
     * @expectedException \Apigee\Edge\Api\Management\Exception\DeveloperNotFoundException
     */
    public function testGetDeveloperByApp(): void
    {
        $developer = (object) [
            'createdAt' => time() * 1000,
            'lastModifiedAt' => time() * 1000,
            'createdBy' => 'phpunit@example.com',
            'lastModifiedBy' => 'phpunit@example.com',
            'name' => 'phpunit',
            'firstName' => 'phpunit',
            'lastName' => 'phpunit',
            'developerId' => 'phpunit',
            'email' => 'phpunit@example.com',
            'organizationName' => 'phpunit',
            'attributes' => [],
            'companies' => [],
            'status' => DeveloperInterface::STATUS_ACTIVE,
        ];
        /** @var \Apigee\Edge\Api\Management\Controller\DeveloperControllerInterface $controller */
        $controller = static::entityController(static::mockApiClient());
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = static::mockApiClient()->getMockHttpClient();
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode(['developer' => [$developer]])));
        // Non-empty response payload can be parsed.
        $this->assertInstanceOf(DeveloperInterface::class, $controller->getDeveloperByApp('app1'));
        // Empty response should trigger an exception.
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode(['developer' => []])));
        $controller->getDeveloperByApp('app1');
    }

    /**
     * @inheritDoc
     */
    protected static function getEntityToTestUrlEncoding(): EntityInterface
    {
        // Use a developer email that makes use of URL encoding to test that CRUD works with encoding.
        return new Developer([
            'email' => 'php+unit+test@example.com',
            'firstName' => static::randomGenerator()->displayName(),
            'lastName' => static::randomGenerator()->displayName(),
            'userName' => static::randomGenerator()->machineName(),
            'attributes' => new AttributesProperty([static::randomGenerator()->machineName() => static::randomGenerator()->machineName()]),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function entityIdShouldBeUsedInPagination(EntityInterface $entity): string
    {
        /* @var \Apigee\Edge\Api\Management\Entity\DeveloperInterface $entity */
        return $entity->getEmail();
    }

    /**
     * @inheritdoc
     */
    protected static function entityController(ClientInterface $client = null): EntityControllerTesterInterface
    {
        return static::developerController($client);
    }

    /**
     * @inheritdoc
     */
    protected static function getNewEntity(): EntityInterface
    {
        return static::getNewDeveloper(!TestClientFactory::isOfflineClient(static::defaultAPIClient()));
    }

    /**
     * @inheritdoc
     */
    protected function entityForUpdateTest(EntityInterface $existing): EntityInterface
    {
        /* @var \Apigee\Edge\Api\Management\Entity\DeveloperInterface $existing */
        return static::getUpdatedDeveloper($existing, !TestClientFactory::isOfflineClient(static::defaultAPIClient()));
    }

    /**
     * @inheritdoc
     */
    protected static function entityCreateOperationTestController(): EntityCreateOperationTestControllerTesterInterface
    {
        return new EntityCreateOperationControllerTester(static::entityController());
    }
}
