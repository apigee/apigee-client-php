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

namespace Apigee\Edge\Tests\Test;

use Apigee\Edge\Client;
use Apigee\Edge\HttpClient\Utility\Builder;
use Apigee\Edge\Tests\Test\HttpClient\MockHttpClient;
use Apigee\Edge\Tests\Test\HttpClient\MockHttpClientInterface;
use GuzzleHttp\Psr7\Response;

class MockClient extends OfflineClientBase implements OfflineClientInterface
{
    /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClientInterface */
    private $httpClient;

    /**
     * MockClient constructor.
     */
    public function __construct()
    {
        $this->httpClient = new MockHttpClient();
        // Return an empty JSON for all requests by default.
        $this->httpClient->setDefaultResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) [])));
        parent::__construct([Client::CONFIG_HTTP_CLIENT_BUILDER => new Builder($this->httpClient)]);
    }

    /**
     * {@inheritdoc}
     */
    public function getMockHttpClient(): MockHttpClientInterface
    {
        return $this->httpClient;
    }
}
