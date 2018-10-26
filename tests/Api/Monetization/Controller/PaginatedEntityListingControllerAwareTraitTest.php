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

use Apigee\Edge\Api\Monetization\Controller\PaginatedEntityListingControllerAwareTrait;
use Apigee\Edge\Api\Monetization\Controller\PaginatedListingHelperTrait;
use Apigee\Edge\Api\Monetization\Serializer\EntitySerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Apigee\Edge\Tests\Test\Controller\MockClientAwareTrait;
use Apigee\Edge\Tests\Test\Entity\MockEntity;
use Apigee\Edge\Utility\JsonDecoderAwareTrait;
use Apigee\Edge\Utility\ResponseToArrayHelper;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;

/**
 * Class PaginatedEntityListingControllerAwareTraitTest.
 *
 * @monetization
 */
class PaginatedEntityListingControllerAwareTraitTest extends TestCase
{
    use MockClientAwareTrait;

    public function testEntityListing(): void
    {
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = static::mockApiClient()->getMockHttpClient();
        $httpClient->setDefaultResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) [[]])));
        $obj = new PaginatedEntityListingControllerAwareTraitClass(static::mockApiClient());
        $obj->getEntities();
        $this->assertEquals('all=true', static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getQuery());
        $obj->getPaginatedEntityList();
        $this->assertEquals('page=1', static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getQuery());
        $obj->getPaginatedEntityList(1, 2);
        $this->assertEquals('page=2&size=1', static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getQuery());
    }
}

class PaginatedEntityListingControllerAwareTraitClass
{
    use EntityListingControllerTrait;
    use PaginatedListingHelperTrait;
    use PaginatedEntityListingControllerAwareTrait;
    use ResponseToArrayHelper;
    use JsonDecoderAwareTrait;

    /**
     * @var \Apigee\Edge\ClientInterface
     */
    protected $client;

    /**
     * @inheritDoc
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri('');
    }

    /**
     * @inheritdoc
     */
    protected function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return MockEntity::class;
    }

    /**
     * @inheritdoc
     */
    protected function getEntitySerializer(): EntitySerializerInterface
    {
        return new EntitySerializer();
    }

    /**
     * @inheritdoc
     */
    protected function jsonDecoder(): JsonDecode
    {
        return new JsonDecode();
    }
}
