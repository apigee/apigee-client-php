<?php

/*
 * Copyright 2019 Google LLC
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
use Apigee\Edge\ClientInterface;
use Apigee\Edge\HttpClient\Utility\Builder;
use Apigee\Edge\Tests\Test\HttpClient\MockHttpClient;
use Apigee\Edge\Tests\Test\HttpClient\Plugin\InMemoryOauthTokenStorage;
use Apigee\Edge\Tests\Test\HttpClient\Plugin\MockHybridOauth2;
use Apigee\Edge\Tests\Test\HttpClient\Utility\TestJournal;
use GuzzleHttp\Psr7\Response;
use Http\Client\Exception\TransferException;
use PHPUnit\Framework\TestCase;

/**
 * Class HybridOauth2Test.
 *
 * @group client
 * @small
 */
class HybridOauth2Test extends TestCase
{
    private const API_ENDPOINT = 'http://api.example.com/v1';

    /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient */
    protected static $httpClient;

    /** @var \Apigee\Edge\HttpClient\Utility\JournalInterface */
    private $journal;

    /** @var \Apigee\Edge\ClientInterface */
    private $client;

    /** @var \Apigee\Edge\HttpClient\Plugin\Authentication\OauthTokenStorageInterface */
    private $tokenStorage;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        // Use the Mock HTTP Client for all requests.
        self::$httpClient = new MockHttpClient();
        parent::setUpBeforeClass();
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tokenStorage = new InMemoryOauthTokenStorage();
        $this->journal = new TestJournal();
        $this->client = $this->buildClient();
    }

    /**
     * @expectedException \Apigee\Edge\Exception\HybridOauth2AuthenticationException
     */
    public function testIncorrectCredentials(): void
    {
        // Auth server respond with 401 for the provided credentials.
        static::$httpClient->addResponse(new Response(401));
        $this->client->get('');
    }

    /**
     * @expectedException \Apigee\Edge\Exception\HybridOauth2AuthenticationException
     */
    public function testAuthServerError(): void
    {
        // Auth server is unavailable.
        static::$httpClient->addException(new TransferException());
        $this->client->get('');
    }

    public function testOauthReAuthenticationAfterExpiredAccessToken(): void
    {
        // Part 1: Add all of the responses to the mock client.
        $body = [
            'access_token' => 'access_token',
            'expires_in' => 60,
            'token_type' => 'Bearer',
        ];
        // Response A: Auth server returns a new access token.
        static::$httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) $body)));

        // Response B: Successful response to an authorised API call from API server.
        static::$httpClient->addResponse(new Response(200));

        // Response C: Another successful response to an authorised API call from API server.
        static::$httpClient->addResponse(new Response(200));

        // Response D: API server answers with authentication error. (Mimic expired token.)
        static::$httpClient->addResponse(new Response(401));

        $body = [
            'access_token' => 'access_token',
            'expires_in' => 60,
            'token_type' => 'Bearer',
        ];
        // Response E: Auth server returns a new access token.
        static::$httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) $body)));

        // Response F: Successful response to an authorised API call from API server.
        static::$httpClient->addResponse(new Response(200));

        // Part 2: Make calls to the API.

        // Calling the API - should use the responses A and B.
        $this->client->get('');

        // Calling the API - should use the response C.
        $this->client->get('');

        // Calling the API, but now it returns response D (401, an expired token).
        // It should then request a new token (response E) and then call the API (response F).
        $this->client->get('');

        // Part 3: Inspect the requests made above.

        /** @var \Psr\Http\Message\RequestInterface[] $requests */
        $requests = $this->journal->getRequests();

        // Check that first call to the API makes a request for a token first.
        $request = array_shift($requests);
        $this->assertEquals(MockHybridOauth2::AUTH_SERVER, (string) $request->getUri());
        $this->assertEmpty($request->getHeaderLine('Authorization'));

        // Check that first API call includes the bearer token authorization header.
        $request = array_shift($requests);
        $this->assertEquals(self::API_ENDPOINT, (string) $request->getUri());
        $this->assertEquals('Bearer access_token', $request->getHeaderLine('Authorization'));

        // Check that second API call did not need to request a new token, as it was already stored.
        $request = array_shift($requests);
        $this->assertEquals(self::API_ENDPOINT, (string) $request->getUri());
        $this->assertEquals('Bearer access_token', $request->getHeaderLine('Authorization'));

        // Third call to the API - this one returned the 401 response, so it should be for a new token.
        $request = array_shift($requests);
        $this->assertEquals(MockHybridOauth2::AUTH_SERVER, (string) $request->getUri());
        $this->assertEmpty($request->getHeaderLine('Authorization'));

        // Check the third API call concludes by making a request to the API endpoint with the bearer token header.
        $request = array_shift($requests);
        $this->assertEquals(self::API_ENDPOINT, (string) $request->getUri());
        $this->assertEquals('Bearer access_token', $request->getHeaderLine('Authorization'));
    }

    /**
     * Builds a client for the test.
     *
     * @param array $options
     *   Client options.
     *
     * @return \Apigee\Edge\ClientInterface
     *   API client for the test.
     */
    private function buildClient(array $options = []): ClientInterface
    {
        $options = array_merge([Client::CONFIG_HTTP_CLIENT_BUILDER => new Builder(static::$httpClient), Client::CONFIG_JOURNAL => $this->journal], $options);
        $email = 'test@example.com';
        // Fake private key.
        $privateKey = '-----BEGIN RSA PRIVATE KEY-----
MIIEogIBAAKCAQEAopybn3R6gbx/iKdpy8XG4ZBCCM+1J33/qg0j0zSUTztJtaJn
OE5CjyiKZPW2r8vNFzqEh77WxbaiZFOfJxGrOlOgAFLMBRX+4nZaxpUphycpL8bs
UZWTbf7NPDIKWC8KUWegdtNU9rLMnyLc/UFmosgZ/ejNLiLKjW7eod8PoGdJHPBW
FH3yxFW8o0R/NHd4nvEU/GDLKreauZEa0RJRAkQffL5JN6vL7KZEU4JJE80ItMk0
8CvFvhYr7HV0pvRfO4qncFiodCINtJFoCdRXPALvoP0dImuRVJnYKjysUjfwhZ0v
1KJJVGAdy3IsrrPV4RWnEZJSpYXls/Zo0TTNvwIDAQABAoIBADRWv+hU71FuwIXV
jMHfyKG1cuuvHxm0/mNXk15ZoBrYdGMYAK0o01eMru7L/58Zs8t0NFAU5sAAhshP
+fvzPe+qFufDvpMsfzuY6KLeQ92Shzkvh8TCpg9TYe+sE5RKn8GP3yAf6Ur2RdI6
wHJraIOgcG0/Tzs4S4W2V8Y2K6tuz61JrELNw1hBvZJX0xOptXPIwXJZpB/XFK4w
/qX1xqIrG1zRgbxdJcUOZbhP71KxjXkn2OrKsgSQ2PhOjMu5PesyPle9EyK942Dz
43UNlqZxrZTOdXHgUf8pN9LN08/q3ePFv4dfcBrI+x/kI2rij2hQcaxr6uv2akJw
UYDEtZECgYEA1O4b2M+k+7Z0EjDHvncMIxrRuiuQHg8VrihMlh3CAV/dFWuOfxW/
6XOBvs8zBKrQKr5vWNP5xnkJkLBxCkcKGLv/ufVnnTw2XWvCspfaVI3414FHqAHM
KDVQZie0KHhb4koevlCs8hErgz0LbBtbguSwbxQH6o4YFYGzF972GwsCgYEAw4Dt
Rt5xP2jjtO6M5qjTqsp2Cl0k99lB5YNW26gSC5/CjTYYJEHUK0NtD+J3Uio1fgeR
fbUXXFB+yfgJTh5OgASsn4rGMU3NRiGkDq7c9cp1VDdZfJlnj8Uf87iH/3LdLKmA
Gjf5bMoS0Vo/x4FJZKVCdf7hbkhMEnMJ6bP1qJ0CgYAf08eNzNvYVBldbrUsnxbN
WIDo1wIfvBl9gsCP37rUAcRGI2GVVWbuOjm2j7oMhIvBF94E5Qp8xDLN0dHTu3Ki
59b9sNTgB9QIescLcu9LnD0J2WUgk8Q0bmOqIV0of3Ucif+2atCvDin23/UJH725
/vzwXYohYUPwUwa2FrmqqQKBgBwiNoMHSba9Sl9kIMSksOkX/4qYQtSj4Ba+Isaz
Vf10PRQDH9A/5N9g8ZXimhcp2c7MGgTEBZuUPdqkpfom5FcJ1SmUV9cKgirAdpJi
WYvJWb8HxAnpLX0D+gghmviIuAlQTw321h7wqWqVGS6FjWE9YuYGCrzLDJ9vDJMk
AYsFAoGASW35IT8ObZT1Ml8lYH/sxNRG4+1UkZmzyBnGLZjs49HtWLqhqSodd8Q8
PuhRwlv1CIQmubIR2B/tRXVqi9488KQhx2tt85UKWBfGw54pTl0AAcBlET6kBQ2S
ol+LjqszLKpNmRhBvWcve/wbsMRWjdIk9ISmX5hCxQjpDobR52o=
-----END RSA PRIVATE KEY-----';

        return new Client(
            new MockHybridOauth2($email, $privateKey, $this->tokenStorage, static::$httpClient, $this->journal),
            self::API_ENDPOINT,
            $options
        );
    }
}
