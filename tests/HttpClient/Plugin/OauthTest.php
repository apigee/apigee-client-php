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

namespace Apigee\Edge\Tests\HttpClient\Plugin;

use Apigee\Edge\Client;
use Apigee\Edge\HttpClient\Utility\Builder;
use Apigee\Edge\Tests\Test\HttpClient\Plugin\InMemoryOauthTokenStorage;
use Apigee\Edge\Tests\Test\HttpClient\Plugin\MockOauth;
use Apigee\Edge\Tests\Test\HttpClient\Utility\TestJournal;
use GuzzleHttp\Psr7\Response;
use Http\Client\Exception\TransferException;
use Http\Mock\Client as MockClient;
use PHPUnit\Framework\TestCase;

/**
 * Class OauthTest.
 *
 * @group client
 * @group mock
 * @group offline
 * @small
 */
class OauthTest extends TestCase
{
    private const API_ENDPOINT = 'http://api.example.com/v1';
    /** @var \Http\Mock\Client */
    protected static $httpClient;

    /** @var \Apigee\Edge\HttpClient\Utility\JournalInterface */
    private $journal;

    /** @var \Apigee\Edge\ClientInterface */
    private $client;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        // Use the Mock HTTP Client for all requests.
        self::$httpClient = new MockClient();
        parent::setUpBeforeClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->journal = new TestJournal();
        $this->client = new Client(new MockOauth('', '', new InMemoryOauthTokenStorage(), static::$httpClient, $this->journal),
            self::API_ENDPOINT, [Client::CONFIG_HTTP_CLIENT_BUILDER => new Builder(static::$httpClient), \Apigee\Edge\Client::CONFIG_JOURNAL => $this->journal]);
    }

    /**
     * @expectedException \Apigee\Edge\Exception\OauthAuthenticationException
     */
    public function testIncorrectClientIdSecret(): void
    {
        // Auth server respond with 401 for the provided client id and secret.
        static::$httpClient->addResponse(new Response(401));
        $this->client->get('');
    }

    /**
     * @expectedException \Apigee\Edge\Exception\OauthAuthenticationException
     */
    public function testAuthServerError(): void
    {
        // Auth server is unavailable.
        static::$httpClient->addException(new TransferException());
        $this->client->get('');
    }

    public function testOauthReAuthenticationAfterExpiredAccessToken(): void
    {
        $body = [
            'access_token' => 'access_token',
            'expires_in' => 60,
            'refresh_token' => 'refresh_token',
        ];
        // Auth server returns a new access token.
        static::$httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) $body)));
        // API server answers with authentication error. (Mimic expired token.)
        static::$httpClient->addResponse(new Response(401));
        $body = [
            'access_token' => 'new_access_token',
            'expires_in' => 60,
            'refresh_token' => 'new_refresh_token',
        ];
        // Auth server answers with a new access token for the refresh token.
        static::$httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) $body)));
        // Successful response to an authorised API call from API server.
        static::$httpClient->addResponse(new Response(200));
        $this->client->get('');
        /** @var \Psr\Http\Message\RequestInterface[] $requests */
        $requests = $this->journal->getRequests();
        // Client credential and secret has been sent to the authorization server.
        $request = array_shift($requests);
        $this->assertEquals(MockOauth::AUTH_SERVER, (string) $request->getUri());
        $this->assertEquals('Basic bW9ja3VzZXI6bW9ja3NlY3JldA==', $request->getHeaderLine('Authorization'));
        // API server answers with authentication error so client asks a new authentication token by using the
        // refresh token from the previous request.
        $request = array_shift($requests);
        $this->assertEquals(MockOauth::AUTH_SERVER, (string) $request->getUri());
        $requestBody = [];
        parse_str((string) $request->getBody(), $requestBody);
        $this->assertEquals('refresh_token', $requestBody['grant_type']);
        $this->assertEquals('refresh_token', $requestBody['refresh_token']);
        // Auth server answers with a new access token. This access token is used to authenticate the
        // management API call.
        $request = array_shift($requests);
        $this->assertEquals(self::API_ENDPOINT, (string) $request->getUri());
        $this->assertEquals('Bearer new_access_token', $request->getHeaderLine('Authorization'));
    }

    public function testOauthReAuthenticationAfterExpiredRefreshToken(): void
    {
        $body = [
            'access_token' => 'access_token',
            'expires_in' => 60,
            'refresh_token' => 'refresh_token',
        ];
        // Auth server returns a new access token.
        static::$httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) $body)));
        // API server answers with authentication error. (Mimic expired token.)
        static::$httpClient->addResponse(new Response(401));
        // API server answers with authentication error. (Mimic expired refresh token.)
        static::$httpClient->addResponse(new Response(401));
        $body = [
            'access_token' => 'new_access_token',
            'expires_in' => 60,
            'refresh_token' => 'new_refresh_token',
        ];
        // Auth server answers with a new access token for the client id and secret.
        static::$httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) $body)));
        // Successful response to an authorised API call from API server.
        static::$httpClient->addResponse(new Response(200));
        $this->client->get('');
        /** @var \Psr\Http\Message\RequestInterface[] $requests */
        $requests = $this->journal->getRequests();
        // Client credential and secret has been sent to the authorization server.
        $request = array_shift($requests);
        $this->assertEquals(MockOauth::AUTH_SERVER, (string) $request->getUri());
        $this->assertEquals('Basic bW9ja3VzZXI6bW9ja3NlY3JldA==', $request->getHeaderLine('Authorization'));
        // API server answers with authentication error so client asks a new authentication token by using the
        // refresh token from the previous request.
        $request = array_shift($requests);
        $this->assertEquals(MockOauth::AUTH_SERVER, (string) $request->getUri());
        $requestBody = [];
        parse_str((string) $request->getBody(), $requestBody);
        $this->assertEquals('refresh_token', $requestBody['grant_type']);
        $this->assertEquals('refresh_token', $requestBody['refresh_token']);
        // Refresh token expired so client must re-authenticate with client id and secret again.
        $request = array_shift($requests);
        $this->assertEquals(MockOauth::AUTH_SERVER, (string) $request->getUri());
        $this->assertEquals('Basic bW9ja3VzZXI6bW9ja3NlY3JldA==', $request->getHeaderLine('Authorization'));
        // Auth server answers with a new access token. This access token is used to authenticate the
        // management API call.
        $request = array_shift($requests);
        $this->assertEquals(self::API_ENDPOINT, (string) $request->getUri());
        $this->assertEquals('Bearer new_access_token', $request->getHeaderLine('Authorization'));
    }
}
