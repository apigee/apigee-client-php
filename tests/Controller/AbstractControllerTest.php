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
use Apigee\Edge\HttpClient\Utility\Builder;
use Apigee\Edge\Tests\Test\Entity\MockEntity;
use Apigee\Edge\Tests\Test\HttpClient\Plugin\NullAuthentication;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Mock\Client as MockClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

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
    /** @var \Apigee\Edge\Controller\AbstractEntityController */
    private static $stub;

    /** @var \Http\Client\HttpClient */
    private static $mockClient;

    /** @var \Apigee\Edge\HttpClient\ClientInterface */
    private static $client;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$mockClient = new MockClient();
        static::$client = new Client(new NullAuthentication(), null, [Client::CONFIG_HTTP_CLIENT_BUILDER => new Builder(static::$mockClient)]);
        $client = static::$client;
        static::$stub = new class($client) extends AbstractEntityController {
            /**
             * @inheritdoc
             */
            protected function getBaseEndpointUri(): UriInterface
            {
                return $this->client->getUriFactory()->createUri('');
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
     * @expectedException \Apigee\Edge\Exception\ApiResponseException
     * @expectedExceptionMessage Unable to parse response with unknown type. Response body: <xml></xml>
     */
    public function testParseResponseWithUnknownContentType(): void
    {
        static::$mockClient->addResponse(new Response(200, [], '<xml></xml>'));
        $response = static::$client->sendRequest(new Request('GET', ''));
        static::$stub->toArray($response);
    }

    /**
     * @expectedException \Apigee\Edge\Exception\InvalidJsonException
     */
    public function testParseResponseWithInvalidJson(): void
    {
        static::$mockClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], ''));
        $response = static::$client->sendRequest(new Request('GET', ''));
        static::$stub->toArray($response);
    }
}
