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

use Apigee\Edge\Controller\AbstractEntityController;
use Apigee\Edge\HttpClient\Client;
use Apigee\Edge\HttpClient\ClientInterface;
use Apigee\Edge\HttpClient\Utility\Builder;
use Apigee\Edge\Tests\Test\Mock\MockEntity;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Discovery\UriFactoryDiscovery;
use Http\Mock\Client as MockClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use function GuzzleHttp\Psr7\stream_for;

/**
 * Class AbstractEntityControllerTest.
 *
 *
 * @group controller
 * @group mock
 * @group offline
 * @small
 */
class AbstractControllerTest extends TestCase
{
    private static $stub;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$stub = new class() extends AbstractEntityController {
            private $mockClient;

            private $rebuild = true;

            /**
             * @param \Apigee\Edge\HttpClient\ClientInterface|null $client
             * @param array $entityNormalizers
             */
            public function __construct(
                ?ClientInterface $client = null,
                $entityNormalizers = []
            ) {
                parent::__construct($client, $entityNormalizers);
                $this->mockClient = new MockClient();
            }

            /**
             * @inheritdoc
             */
            protected function getBaseEndpointUri(): UriInterface
            {
                $uriFactory = UriFactoryDiscovery::find();

                return $uriFactory->createUri('');
            }

            /**
             * Allows to set returned response by the mock HTTP client.
             *
             * @param \Psr\Http\Message\ResponseInterface $response
             */
            public function addResponse(ResponseInterface $response): void
            {
                $this->mockClient->addResponse($response);
                $this->rebuild = true;
            }

            /**
             * @return \Apigee\Edge\HttpClient\Client
             */
            public function getClient(): Client
            {
                if ($this->rebuild) {
                    $builder = new Builder($this->mockClient);
                    $this->client = new Client(null, $builder);
                    $this->rebuild = false;
                }

                return $this->client;
            }

            /**
             * Exposes protected parseResponseToArray method for testing.
             *
             * @param \Psr\Http\Message\ResponseInterface $response
             *
             * @return array
             */
            public function toArray(ResponseInterface $response)
            {
                return $this->responseToArray($response);
            }

            /**
             * @inheritdoc
             */
            protected function getEntityClass(): string
            {
                return MockEntity::class;
            }
        };
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testParseResponseWithUnknownContentType(): void
    {
        /** @var \Apigee\Edge\HttpClient\ClientInterface $client */
        $client = static::$stub->getClient();
        static::$stub->addResponse(new Response());
        $response = $client->sendRequest(new Request('GET', ''));
        static::$stub->toArray($response);
    }

    /**
     * @expectedException \Apigee\Edge\Exception\InvalidJsonException
     */
    public function testParseResponseWithInvalidJson(): void
    {
        /** @var \Apigee\Edge\HttpClient\ClientInterface $client */
        $client = static::$stub->getClient();
        static::$stub->addResponse(new Response(200, ['Content-Type' => 'application/json'], stream_for('')));
        $response = $client->sendRequest(new Request('GET', ''));
        static::$stub->toArray($response);
    }
}
