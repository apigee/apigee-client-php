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

namespace Apigee\Edge\Tests\Test\Mock;

use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;
use Http\Adapter\Guzzle6\Client;
use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Http\Message\Formatter;
use Http\Message\Formatter\SimpleFormatter;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

/**
 * Debugger client that logs information about the sent requests to the API.
 *
 * Requires Guzzle >= 6.1.0. because on_stats option is only available since that version.
 *
 * @see https://github.com/guzzle/guzzle/pull/1202
 */
class DebuggerClient implements HttpClient, HttpAsyncClient
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;
    /**
     * @var \Http\Message\Formatter
     */
    private $formatter;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var string
     */
    private $logFormat;

    public function __construct(array $config = [], Formatter $formatter = null, LoggerInterface $logger = null, string $logFormat = '{request_formatted} {response_formatted}')
    {
        if (null === $formatter) {
            $formatter = new SimpleFormatter();
        }
        if (null === $logger) {
            $logger = new NullLogger();
        }
        $this->formatter = $formatter;
        $this->logger = $logger;
        $this->logFormat = $logFormat;
        $config += [
            RequestOptions::ON_STATS => function (TransferStats $stats) use ($logger, $formatter, $logFormat): void {
                /** @var RequestInterface $request */
                $request = $stats->getRequest()->withoutHeader('Authorization');
                $time_stats = array_filter($stats->getHandlerStats(), function ($key) {
                    return preg_match('/_time$/', $key);
                }, ARRAY_FILTER_USE_KEY);
                $time_stats = array_map(function ($stat) {
                    return round($stat, 3);
                }, $time_stats);
                $context = [
                    'request' => $request,
                    'request_formatted' => $formatter->formatRequest($request),
                    'stats' => $stats,
                    'time_stats' => var_export($time_stats, true),
                ];
                if ($stats->hasResponse()) {
                    $context['response'] = $stats->getResponse();
                    $context['response_formatted'] = $formatter->formatResponse($stats->getResponse());
                } else {
                    $context['error'] = $stats->getHandlerErrorData();
                }
                $logger->log(LogLevel::DEBUG, $logFormat, $context);
            },
        ];
        $this->client = Client::createWithConfig($config);
    }

    /**
     * @inheritdoc
     */
    public function sendAsyncRequest(RequestInterface $request)
    {
        return $this->client->sendAsyncRequest($request);
    }

    /**
     * @inheritdoc
     */
    public function sendRequest(RequestInterface $request)
    {
        return $this->client->sendRequest($request);
    }
}
