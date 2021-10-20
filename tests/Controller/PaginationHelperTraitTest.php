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

namespace Apigee\Edge\Tests\Controller;

use Apigee\Edge\Api\Management\Controller\OrganizationController;
use Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Controller\PaginatedEntityIdListingControllerTrait;
use Apigee\Edge\Controller\PaginatedEntityListingControllerTrait;
use Apigee\Edge\Controller\PaginationHelperTrait;
use Apigee\Edge\Serializer\EntitySerializer;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Apigee\Edge\Serializer\JsonDecode as ApigeeEdgeJsonDecode;
use Apigee\Edge\Tests\Test\Controller\MockClientAwareTrait;
use Apigee\Edge\Tests\Test\Entity\MockEntity;
use Apigee\Edge\Utility\ResponseToArrayHelper;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;

/**
 * Tests previously uncovered edge cases in PaginationHelperTrait.
 */
class PaginationHelperTraitTest extends TestCase
{
    use MockClientAwareTrait;

    /** @var \Apigee\Edge\Tests\Controller\PaginationHelperTraitTestClass */
    protected static $testController;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$testController = new PaginationHelperTraitTestClass();
    }

    public function testListEntitiesWithoutPagerWithEmptyResponse(): void
    {
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = static::mockApiClient()->getMockHttpClient();
        foreach ([true, false] as $cpsEnabled) {
            $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], $this->getOrgLoadResponsePayload($cpsEnabled)));
            $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode([[]])));
            $this->assertEmpty(static::$testController->getEntities(), $cpsEnabled ? 'CPS supported' : 'CPS not supported');
        }
    }

    public function testListEntityIdsWithoutPagerWithEmptyResponse(): void
    {
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = static::mockApiClient()->getMockHttpClient();
        foreach ([true, false] as $cpsEnabled) {
            $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], $this->getOrgLoadResponsePayload($cpsEnabled)));
            $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode([])));
            $this->assertEmpty(static::$testController->getEntityIds(), $cpsEnabled ? 'CPS supported' : 'CPS not supported');
        }
    }

    public function testListEntityIdsWithoutPagerWithMoreResults(): void
    {
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = static::mockApiClient()->getMockHttpClient();
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], $this->getOrgLoadResponsePayload()));
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode(['first', 'second'])));
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode(['second', 'third'])));
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode([])));
        $this->assertCount(3, static::$testController->getEntityIds(), 'CPS enabled');
    }

    /**
     * @expectedException \PHPUnit\Framework\Error\Notice
     * @expectedExceptionMessage Apigee Edge PHP Client: Simulating CPS pagination on an organization that does not have CPS support. https://docs.apigee.com/api-platform/reference/cps
     */
    public function testWithoutCpsNotice(): void
    {
        // Make sure CPS notice suppressing is disabled.
        putenv('APIGEE_EDGE_PHP_CLIENT_SUPPRESS_CPS_SIMULATION_NOTICE=0');
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = static::mockApiClient()->getMockHttpClient();
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], $this->getOrgLoadResponsePayload(false)));
        static::$testController->getEntityIds(static::$testController->createPager());
    }

    public function testWithoutCpsNoticeSuppress(): void
    {
        // Make sure CPS notice suppressing is enabled.
        putenv('APIGEE_EDGE_PHP_CLIENT_SUPPRESS_CPS_SIMULATION_NOTICE=1');
        $httpClient = static::mockApiClient()->getMockHttpClient();
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], $this->getOrgLoadResponsePayload(false)));
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode(['first', 'second'])));
        $this->assertCount(2, static::$testController->getEntityIds(static::$testController->createPager()));
    }

    /**
     * @expectedException \Apigee\Edge\Exception\RuntimeException
     * @expectedExceptionMessage CPS simulation error: "foo" does not exist.
     */
    public function testListEntityIdsWithoutCpsWithInvalidStartKey(): void
    {
        $this->iniSet('error_reporting', 'E_ALL & ~E_NOTICE');
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = static::mockApiClient()->getMockHttpClient();
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], $this->getOrgLoadResponsePayload(false)));
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode(['first', 'second'])));
        static::$testController->getEntityIds(static::$testController->createPager(0, 'foo'));
    }

    public function testListEntityIdsWithoutCps(): void
    {
        putenv('APIGEE_EDGE_PHP_CLIENT_SUPPRESS_CPS_SIMULATION_NOTICE=1');
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = static::mockApiClient()->getMockHttpClient();
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], $this->getOrgLoadResponsePayload(false)));
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode(['first', 'second'])));
        $this->assertCount(2, static::$testController->getEntityIds());
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], $this->getOrgLoadResponsePayload(false)));
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode(['first', 'second', 'third', 'fourth'])));
        $result = static::$testController->getEntityIds(static::$testController->createPager(2, 'third'));
        $this->assertCount(2, $result);
        $this->assertContains('third', $result);
        $this->assertContains('fourth', $result);
    }

    protected function getOrgLoadResponsePayload(bool $cpsEnabled = true): string
    {
        $properties = [];

        if ($cpsEnabled) {
            $properties[] = (object) [
                'name' => 'features.isCpsEnabled',
                'value' => 'true',
            ];
        }

        $org = [
            'createdAt' => time() * 1000,
            'lastModifiedAt' => time() * 1000,
            'createdBy' => 'phpunit@example.com',
            'lastModifiedBy' => 'phpunit@example.com',
            'displayName' => 'phpunit',
            'name' => 'phpunit',
            'environments' => [],
            'properties' => (object) ['property' => $properties],
            'type' => 'paid',
        ];

        return json_encode((object) $org);
    }
}

class PaginationHelperTraitTestClass
{
    use PaginationHelperTrait;
    use PaginatedEntityIdListingControllerTrait;
    use PaginatedEntityListingControllerTrait;
    use EntityListingControllerTrait;
    use ResponseToArrayHelper;
    use MockClientAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function getOrganisationName(): string
    {
        return 'phpunit';
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->getClient()->getUriFactory()->createUri('');
    }

    /**
     * {@inheritdoc}
     */
    protected function getClient(): ClientInterface
    {
        return static::mockApiClient();
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return MockEntity::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntitySerializer(): EntitySerializerInterface
    {
        return new EntitySerializer();
    }

    protected function jsonDecoder(): JsonDecode
    {
        return new ApigeeEdgeJsonDecode();
    }

    /**
     * {@inheritdoc}
     */
    protected function getOrganizationController(): OrganizationControllerInterface
    {
        return new OrganizationController(static::mockApiClient());
    }
}
