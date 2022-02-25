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
use Apigee\Edge\Tests\Test\Controller\MockClientAwareTrait;
use Apigee\Edge\Tests\Test\Entity\MockEntity;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class AbstractEntityControllerTest.
 *
 * @group controller
 * @group mock
 * @small
 */
class AbstractControllerTest extends TestCase
{
    use MockClientAwareTrait;

    /** @var \Apigee\Edge\Controller\AbstractEntityController */
    private static $stub;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $client = static::mockApiClient();
        static::$stub = new class($client) extends AbstractEntityController {
            /**
             * {@inheritdoc}
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
             * {@inheritdoc}
             */
            protected function getEntityClass(): string
            {
                return MockEntity::class;
            }
        };
    }

    public function testParseResponseWithUnknownContentType(): void
    {
        $this->expectException('\Apigee\Edge\Exception\ApiResponseException');
        $this->expectExceptionMessage('Unable to parse response with unknown type. Response body: <xml></xml>');

        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = static::mockApiClient()->getMockHttpClient();
        $httpClient->addResponse(new Response(200, [], '<xml></xml>'));
        $response = static::mockApiClient()->sendRequest(new Request('GET', ''));
        static::$stub->toArray($response);
    }

    public function testParseResponseWithInvalidJson(): void
    {
        $this->expectException('\Apigee\Edge\Exception\InvalidJsonException');

        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = static::mockApiClient()->getMockHttpClient();
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], ''));
        $response = static::mockApiClient()->sendRequest(new Request('GET', ''));
        static::$stub->toArray($response);
    }
}
