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

namespace Apigee\Edge\Tests\HttpClient;

use Apigee\Edge\Client;
use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\HttpClient\Utility\Builder;
use Apigee\Edge\Tests\Test\HttpClient\Plugin\NullAuthentication;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\Common\Plugin\HeaderAppendPlugin;
use Http\Client\Exception\NetworkException;
use Http\Client\Exception\RequestException;
use Http\Mock\Client as MockClient;
use PHPUnit\Framework\TestCase;

/**
 * Class ClientTest.
 *
 * @group client
 * @group mock
 * @group offline
 * @small
 */
class ClientTest extends TestCase
{
    /** @var \Http\Mock\Client */
    protected static $httpClient;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        // Use the Mock HTTP Client for all requests.
        self::$httpClient = new MockClient();
        parent::setUpBeforeClass();
    }

    public function testDefaultConfiguration()
    {
        $builder = new Builder(self::$httpClient);
        $client = new Client(new NullAuthentication(), null, [Client::CONFIG_HTTP_CLIENT_BUILDER => $builder]);
        $client->get('/');
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertEquals('https://api.enterprise.apigee.com/v1/', (string) $sent_request->getUri());
        $this->assertEquals($client->getUserAgent(), $sent_request->getHeaderLine('User-Agent'));
        $this->assertEquals('application/json; charset=utf-8', $sent_request->getHeaderLine('Accept'));

        return $client;
    }

    /**
     * @depends testDefaultConfiguration
     *
     * @param \Apigee\Edge\Client $client
     */
    public function testEndpointShouldBeOverridden(\Apigee\Edge\Client $client): void
    {
        $customEndpoint = 'http://example.com';
        $builder = new Builder(self::$httpClient);
        $client = new Client(new NullAuthentication(), $customEndpoint, [Client::CONFIG_HTTP_CLIENT_BUILDER => $builder]);
        $client->get('/');
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertEquals($customEndpoint,"{$sent_request->getUri()->getScheme()}://{$sent_request->getUri()->getHost()}");
    }

    public function testUserAgentShouldBeOverridden(): void
    {
        $builder = new Builder(self::$httpClient);
        $userAgentPrefix = 'Awesome ';
        $client = new Client(new NullAuthentication(), null, [Client::CONFIG_USER_AGENT_PREFIX => $userAgentPrefix, Client::CONFIG_HTTP_CLIENT_BUILDER => $builder]);
        $client->get('/');
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertEquals("{$userAgentPrefix} ({$client->getClientVersion()})", $sent_request->getHeaderLine('User-Agent'));
    }

    public function testRebuildShouldNotRemoveCustomPlugin(): void
    {
        $builder = new Builder(self::$httpClient);
        $builder->addPlugin(new HeaderAppendPlugin(['Foo' => 'bar']));
        $client = new Client(new NullAuthentication(), null, [Client::CONFIG_HTTP_CLIENT_BUILDER => $builder]);
        $client->get('/');
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertEquals('bar', $sent_request->getHeaderLine('Foo'));
    }

    /**
     * @expectedException \Apigee\Edge\Exception\ApiRequestException
     */
    public function testApiNotReachable(): void
    {
        static::$httpClient->addException(new NetworkException('', new Request('GET', 'http://example.com')));
        $builder = new Builder(self::$httpClient);
        $client = new Client(new NullAuthentication(), null, [\Apigee\Edge\Client::CONFIG_HTTP_CLIENT_BUILDER => $builder]);
        $client->get('/');
    }

    /**
     * @expectedException \Apigee\Edge\Exception\ApiRequestException
     */
    public function testInvalidRequest(): void
    {
        static::$httpClient->addException(new RequestException('Invalid request', new Request('GET', 'http://example.com')));
        $builder = new Builder(self::$httpClient);
        $client = new \Apigee\Edge\Client(new NullAuthentication(), null, [Client::CONFIG_HTTP_CLIENT_BUILDER => $builder]);
        $client->get('/');
    }

    /**
     * @expectedException \Apigee\Edge\Exception\ClientErrorException
     * @expectedExceptionCode 404
     */
    public function testApiEndpointNotFound(): void
    {
        static::$httpClient->addResponse(new Response(404));
        $builder = new Builder(self::$httpClient);
        $client = new Client(new NullAuthentication(), null, [Client::CONFIG_HTTP_CLIENT_BUILDER => $builder]);
        $client->get('/');
    }

    /**
     * @expectedException \Apigee\Edge\Exception\ServerErrorException
     * @expectedExceptionCode 500
     */
    public function testServerError(): void
    {
        static::$httpClient->addResponse(new Response(500));
        $builder = new Builder(self::$httpClient);
        $client = new \Apigee\Edge\Client(new NullAuthentication(), null, [Client::CONFIG_HTTP_CLIENT_BUILDER => $builder]);
        $client->get('/');
    }

    public function testClientExceptionWithResponseBody(): void
    {
        $errorCode = 'error code';
        $errorMessage = 'Error message';
        $body = [
            'code' => $errorCode,
            'message' => $errorMessage,
        ];
        static::$httpClient->addResponse(new Response(400, ['Content-Type' => 'application/json'], json_encode((object) $body)));
        $builder = new Builder(static::$httpClient);
        $client = new Client(new NullAuthentication(), null, [Client::CONFIG_HTTP_CLIENT_BUILDER => $builder]);
        try {
            $client->get('/');
        } catch (\Exception $e) {
            $this->assertInstanceOf(ClientErrorException::class, $e);
            /* @var \Apigee\Edge\Exception\ClientErrorException $e */
            $this->assertEquals($e->getEdgeErrorCode(), $errorCode);
            $this->assertEquals($e->getMessage(), $errorMessage);
        }
    }
}
