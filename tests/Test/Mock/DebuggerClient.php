<?php

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

    public function __construct(array $config = [], Formatter $formatter = null, LoggerInterface $logger = null, string $logFormat = '{formatted}')
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
                $logger->log(LogLevel::DEBUG, $logFormat, ['formatted' => $formatter->formatRequest($request), 'stats' => $stats, 'time_stats' => var_export($time_stats, true)]);
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
