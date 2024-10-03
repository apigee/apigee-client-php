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

namespace Apigee\Edge\HttpClient\Utility;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Journal.
 *
 * Stores the last request, response and response exception by using the HistoryPlugin.
 * We did not want to collect and keep all requests, responses and exceptions because that would have increase memory
 * usage of the SDK.
 *
 * @see \Http\Client\Common\Plugin\HistoryPlugin
 */
final class Journal implements JournalInterface
{
    /** @var RequestInterface */
    private $lastRequest;

    /** @var ResponseInterface */
    private $lastResponse;

    /** @var \Http\Client\Exception */
    private $lastException;

    /**
     * Indicates whether the last request was successful or not.
     *
     * @var bool
     */
    private $success = true;

    /**
     * Record a successful call.
     *
     * @param RequestInterface $request Request use to make the call
     * @param ResponseInterface $response Response returned by the call
     */
    public function addSuccess(RequestInterface $request, ResponseInterface $response): void
    {
        $this->lastRequest = $request;
        $this->lastResponse = $response;
        $this->success = true;
    }

    /**
     * Record a failed call.
     *
     * @param RequestInterface $request Request use to make the call
     * @param ClientExceptionInterface $exception Exception returned by the call
     */
    public function addFailure(RequestInterface $request, ClientExceptionInterface $exception): void
    {
        $this->lastRequest = $request;
        $this->lastException = $exception;
    }

    /**
     * @return RequestInterface
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * @return ResponseInterface
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * @return \Http\Client\Exception
     */
    public function getLastException()
    {
        return $this->lastException;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }
}
