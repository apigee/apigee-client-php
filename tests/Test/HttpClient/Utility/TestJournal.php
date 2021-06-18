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

namespace Apigee\Edge\Tests\Test\HttpClient\Utility;

use Apigee\Edge\HttpClient\Utility\JournalInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Journal that notices _all_ requests, responses, exceptions in tests.
 */
final class TestJournal implements JournalInterface
{
    private $requests = [];

    private $responses = [];

    private $exceptions = [];

    /**
     * {@inheritdoc}
     */
    public function addSuccess(RequestInterface $request, ResponseInterface $response): void
    {
        $this->requests[] = $request;
        $this->responses[] = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function addFailure(RequestInterface $request, ClientExceptionInterface $exception): void
    {
        $this->requests[] = $request;
        $this->exceptions[] = $exception;
    }

    /**
     * @return array
     */
    public function getRequests(): array
    {
        return $this->requests;
    }

    /**
     * @return array
     */
    public function getResponses(): array
    {
        return $this->responses;
    }

    /**
     * @return array
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastRequest()
    {
        return end($this->requests);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastResponse()
    {
        return end($this->responses);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastException()
    {
        return end($this->exceptions);
    }
}
