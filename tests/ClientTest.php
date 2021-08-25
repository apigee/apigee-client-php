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

namespace Apigee\Edge\Tests;

use Apigee\Edge\Client;
use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\HttpClient\Plugin\Authentication\NullAuthentication;
use Apigee\Edge\HttpClient\Utility\Builder;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\Common\Plugin\HeaderAppendPlugin;
use Http\Client\Exception\NetworkException;
use Http\Client\Exception\RequestException;
use Http\Mock\Client as MockHttpClient;
use PHPUnit\Framework\TestCase;

/**
 * Class ClientTest.
 *
 * @group client
 * @group mock
 * @small
 */
class ClientTest extends TestCase
{
    /** @var \Http\Mock\Client */
    protected static $httpClient;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        // We use the original, undecorated mock http client for this that
        // does not wrap all exceptions to MockHttpClientExceptions.
        static::$httpClient = new MockHttpClient();
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
    public function testEndpointShouldBeOverridden(Client $client): void
    {
        // The Apigee API endpoint URI always contains the API version in path.
        // @see \Apigee\Edge\HttpClient\Plugin\AddPathPlugin::__construct()
        $customEndpoint = 'http://example.com/version_1';
        $builder = new Builder(self::$httpClient);
        $client = new Client(new NullAuthentication(), $customEndpoint, [Client::CONFIG_HTTP_CLIENT_BUILDER => $builder]);
        $client->get('');
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertEquals($client->getUriFactory()->createUri($customEndpoint)->getHost(), $sent_request->getUri()->getHost());
    }

    public function testUserAgentShouldBeOverridden(): void
    {
        $builder = new Builder(self::$httpClient);
        $userAgentPrefix = 'Awesome ';
        $client = new Client(new NullAuthentication(), null, [Client::CONFIG_USER_AGENT_PREFIX => $userAgentPrefix, Client::CONFIG_HTTP_CLIENT_BUILDER => $builder]);
        $client->get('/');
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertEquals("{$userAgentPrefix} ({$client->getClientVersion()}" . "; PHP/" . PHP_VERSION . ")", $sent_request->getHeaderLine('User-Agent'));
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
        $client = new Client(new NullAuthentication(), null, [Client::CONFIG_HTTP_CLIENT_BUILDER => $builder]);
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
        $client = new Client(new NullAuthentication(), null, [Client::CONFIG_HTTP_CLIENT_BUILDER => $builder]);
        $client->get('/');
    }

    /**
     * @expectedException \Apigee\Edge\Exception\ClientErrorException
     * @expectedExceptionCode 400
     */
    public function testRetryPlugin(): void
    {
        static::$httpClient->addResponse(new Response(500));
        static::$httpClient->addResponse(new Response(200));
        $builder = new Builder(self::$httpClient);
        $client = new Client(new NullAuthentication(), null, [
            Client::CONFIG_HTTP_CLIENT_BUILDER => $builder,
            Client::CONFIG_RETRY_PLUGIN_CONFIG => ['retries' => 1],
        ]);
        $response = $client->get('/');
        $this->assertEquals(200, $response->getStatusCode());
        // Do not retry API calls that contained a bad request.
        static::$httpClient->addResponse(new Response(400));
        $client->get('/');
    }

    public function testClientExceptionWithErrorResponse(): void
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

    public function testClientExceptionWithFaultResponse(): void
    {
        $errorCode = 'error code';
        $errorMessage = 'Error message';
        $body = [
            'fault' => (object) [
                'faultstring' => $errorMessage,
                'detail' => (object) ['errorcode' => $errorCode],
            ],
        ];

        static::$httpClient->addResponse(new Response(404, ['Content-Type' => 'application/json'], json_encode((object) $body)));
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
