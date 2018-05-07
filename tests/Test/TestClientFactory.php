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
use Apigee\Edge\ClientInterface;
use Apigee\Edge\HttpClient\Utility\Builder;
use Apigee\Edge\Tests\Test\HttpClient\DebuggerClient;
use Apigee\Edge\Tests\Test\HttpClient\FileSystemMockClient;
use Apigee\Edge\Tests\Test\HttpClient\MockClientInterface;
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
     * @return \Apigee\Edge\ClientInterface
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
            $logHandler = new StreamHandler(__DIR__ . '/../../debug.log');
            // Only print the message.
            $logHandler->setFormatter(new LineFormatter('%message%', null, true));
            $logger = new Logger('debuggerClient', [$logHandler], [new PsrLogMessageProcessor()]);
            $formatter = new CurlCommandFormatter();
            $logFormat = "{request_formatted}\nStats: {time_stats}\n\n";
            $builder = new Builder($rc->newInstance([], $formatter, $logger, $logFormat));
        } else {
            $builder = new Builder($rc->newInstance());
        }

        return new Client($auth, $endpoint, [Client::CONFIG_HTTP_CLIENT_BUILDER => $builder, Client::CONFIG_USER_AGENT_PREFIX => $userAgentPrefix]);
    }

    /**
     * Helper function that returns whether the API client is using a mock HTTP client or not.
     *
     * @param \Apigee\Edge\ClientInterface $client
     *   API client.
     *
     * @return bool
     */
    public static function isMockClient(ClientInterface $client): bool
    {
        return 0 === strpos($client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX);
    }
}
