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
use Apigee\Edge\Tests\Test\HttpClient\FileSystemHttpMockClient;
use Apigee\Edge\Tests\Test\HttpClient\MockHttpClientInterface;
use League\Flysystem\AdapterInterface;

class FileSystemMockClient extends OfflineClientBase implements OfflineClientInterface
{
    /** @var FileSystemHttpMockClient */
    private $httpClient;

    /**
     * FileSystemMockClient constructor.
     *
     * @param AdapterInterface|null $adapter
     */
    public function __construct(?AdapterInterface $adapter = null)
    {
        $this->httpClient = new FileSystemHttpMockClient($adapter);
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
