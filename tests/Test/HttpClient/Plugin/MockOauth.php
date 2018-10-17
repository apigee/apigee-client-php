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

namespace Apigee\Edge\Tests\Test\HttpClient\Plugin;

use Apigee\Edge\Client;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\HttpClient\Plugin\Authentication\Oauth;
use Apigee\Edge\HttpClient\Utility\Builder;
use Apigee\Edge\HttpClient\Utility\JournalInterface;
use Apigee\Edge\Tests\Test\HttpClient\MockHttpClient;
use Apigee\Edge\Tests\Test\HttpClient\Utility\TestJournal;
use Http\Client\HttpClient;
use Http\Message\Authentication\BasicAuth;

/**
 * OAuth authentication plugin that uses mock API client for authorisation.
 */
class MockOauth extends Oauth
{
    public const AUTH_SERVER = 'http://example.com/oauth/token';
    /**
     * @var \Apigee\Edge\HttpClient\Utility\JournalInterface
     */
    private $journal;
    /**
     * @var \Http\Client\HttpClient
     */
    private $httpClient;

    public function __construct(
        string $username,
        string $password,
        \Apigee\Edge\HttpClient\Plugin\Authentication\OauthTokenStorageInterface $token_storage,
        HttpClient $httpClient = null,
        JournalInterface $journal = null,
        ?string $mfa_token = null,
        ?string $client_id = null,
        ?string $client_secret = null,
        ?string $scope = null,
        ?string $auth_server = null
    ) {
        parent::__construct($username, $password, $token_storage, $mfa_token, $client_id, $client_secret, $scope, $auth_server);
        $this->journal = $journal ?: new TestJournal();
        $this->httpClient = $httpClient ?: new MockHttpClient();
    }

    protected function authClient(): ClientInterface
    {
        return new Client(new BasicAuth('mockuser', 'mocksecret'), self::AUTH_SERVER, [
            Client::CONFIG_HTTP_CLIENT_BUILDER => new Builder($this->httpClient),
            Client::CONFIG_JOURNAL => $this->journal,
        ]);
    }
}
