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

namespace Apigee\Edge\Tests\Test\HttpClient;

use Apigee\Edge\Tests\Test\HttpClient\Exception\MockHttpClientException;
use Http\Client\Common\HttpAsyncClientEmulator;
use League\Flysystem\AdapterInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Loads the content of an HTTP response from the file system if available.
 */
class FileSystemHttpMockClient implements MockHttpClientInterface
{
    use HttpAsyncClientEmulator;

    /** @var FileSystemResponseFactory */
    private $fileSystemResponseFactory;

    /**
     * FileSystemMockClient constructor.
     *
     * @param \League\Flysystem\AdapterInterface|null $adapter
     */
    public function __construct(AdapterInterface $adapter = null)
    {
        $this->fileSystemResponseFactory = new FileSystemResponseFactory($adapter);
    }

    /**
     * {@inheritdoc}
     *
     * @see HttpClient::sendRequest
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->fileSystemResponseFactory->createResponseForRequest($request, 200, null, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            throw new MockHttpClientException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
