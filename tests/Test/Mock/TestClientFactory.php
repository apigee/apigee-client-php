<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Tests\Test\Mock;

use Apigee\Edge\HttpClient\Client;
use Apigee\Edge\HttpClient\ClientInterface;
use Apigee\Edge\HttpClient\Utility\Builder;
use Http\Client\HttpClient;
use Http\Message\Authentication\BasicAuth;
use Http\Message\Formatter\CurlCommandFormatter;
use Http\Mock\Client as MockClient;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

/**
 * Class TestClientFactory.
 */
class TestClientFactory
{
    public const OFFLINE_CLIENT_USER_AGENT_PREFIX = 'PHPUNIT';

    /**
     * Factory method that returns a configured API client.
     *
     * @param string|null $fqcn
     *   Fully qualified test name of the HTTP client class.
     *
     * @throws \ReflectionException
     *   By ReflectionClass.
     * @throws \Exception
     *   By StreamHandler.
     *
     * @return ClientInterface
     */
    public function getClient(string $fqcn = null): ClientInterface
    {
        $fqcn = $fqcn ?: getenv('APIGEE_EDGE_PHP_SDK_HTTP_CLIENT') ?: FileSystemMockClient::class;
        $rc = new \ReflectionClass($fqcn);
        if (!$rc->implementsInterface(MockClientInterface::class) && !$rc->implementsInterface(HttpClient::class)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Class must implements either %s interface or %s interface.',
                    MockClientInterface::class,
                    HttpClient::class
                )
            );
        }
        $userAgentPrefix = '';
        if ($rc->implementsInterface(MockClientInterface::class) || $rc->isSubclassOf(MockClient::class)) {
            // The only way to identify whether this is mock HTTP client in tests is this special user agent prefix.
            $userAgentPrefix = self::OFFLINE_CLIENT_USER_AGENT_PREFIX;
        }
        $auth = null;
        $user = getenv('APIGEE_EDGE_PHP_SDK_BASIC_AUTH_USER') ?: '';
        $password = getenv('APIGEE_EDGE_PHP_SDK_BASIC_AUTH_PASSWORD') ?: '';
        if ($user || $password) {
            $auth = new BasicAuth($user, $password);
        }
        $endpoint = getenv('APIGEE_EDGE_PHP_SDK_ENDPOINT') ?: null;
        if (DebuggerClient::class == $rc->getName()) {
            $logHandler = new StreamHandler(__DIR__ . '/../../../debug.log');
            // Only print the message.
            $logHandler->setFormatter(new LineFormatter('%message%', null, true));
            $logger = new Logger('debuggerClient', [$logHandler], [new PsrLogMessageProcessor()]);
            $formatter = new CurlCommandFormatter();
            $logFormat = "{request_formatted}\nStats: {time_stats}\n\n";
            $builder = new Builder($rc->newInstance([], $formatter, $logger, $logFormat));
        } else {
            $builder = new Builder($rc->newInstance());
        }

        return new Client($auth, $builder, $endpoint, $userAgentPrefix);
    }

    /**
     * Helper function that returns whether the API client is using a mock HTTP client or not.
     *
     * @param ClientInterface $client
     *   API client.
     *
     * @return bool
     */
    public static function isMockClient(ClientInterface $client): bool
    {
        return 0 === strpos($client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX);
    }
}
