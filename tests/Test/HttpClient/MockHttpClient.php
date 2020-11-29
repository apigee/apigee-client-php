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
use Http\Message\ResponseFactory;
use Http\Mock\Client;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Decorates PHP-HTTP's mock client.
 *
 * @method void addException(\Exception $exception)
 * @method void setDefaultException(\Exception $defaultException = null)
 * @method void addResponse(ResponseInterface $response)
 * @method void setDefaultResponse(ResponseInterface $defaultResponse = null)
 * @method RequestInterface[] getRequests()
 * @method RequestInterface|false getLastRequest()
 */
class MockHttpClient implements MockHttpClientInterface
{
    use HttpAsyncClientEmulator;

    /** @var \Http\Mock\Client */
    private $decorated;

    /**
     * MockHttpClient constructor.
     *
     * @param \Http\Message\ResponseFactory|null $responseFactory
     */
    public function __construct(ResponseFactory $responseFactory = null)
    {
        $this->decorated = new Client($responseFactory);
    }

    /**
     * @inheritDoc
     */
    public function __call($name, $arguments)
    {
        $object = null;
        if (method_exists($this, $name)) {
            $object = $this;
        } elseif (method_exists($this->decorated, $name)) {
            $object = $this->decorated;
        } else {
            throw new \InvalidArgumentException("Method not found {$name}.");
        }

        return call_user_func_array([$object, $name], $arguments);
    }

    /**
     * {@inheritdoc}
     *
     * @see HttpClient::sendRequest
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->decorated->sendRequest($request);
        } catch (\Exception $e) {
            throw new MockHttpClientException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
