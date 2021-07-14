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

use Apigee\Edge\ClientInterface;
use Apigee\Edge\Tests\Test\HttpClient\DebuggerHttpClient;
use Http\Message\Authentication\BasicAuth;
use Http\Message\Formatter\CurlCommandFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use ReflectionClass;

/**
 * Class TestClientFactory.
 */
class TestClientFactory
{
    /** @var \Apigee\Edge\ClientInterface[] */
    private static $instances = [];

    /**
     * TestClientFactory constructor.
     */
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public function __wakeup(): void
    {
    }

    /**
     * Factory method that returns a configured test API client.
     *
     * @param string|null $fqcn
     *   Fully qualified name of a test API client.
     * @param bool $reset
     *   Enforces factory to rebuild the test client instead of returning it
     *   from its static cache.
     *
     * @return \Apigee\Edge\ClientInterface
     */
    public static function getClient(string $fqcn = null, bool $reset = false): ClientInterface
    {
        $fqcn = $fqcn ?: getenv('APIGEE_EDGE_PHP_CLIENT_API_CLIENT') ?: FileSystemMockClient::class;

        if (!$reset && array_key_exists($fqcn, self::$instances)) {
            return self::$instances[$fqcn];
        }

        try {
            $clientRC = new \ReflectionClass($fqcn);
        } catch (\ReflectionException $e) {
            throw new \InvalidArgumentException("Unable to initialize client class with {$fqcn} name.", $e->getCode(), $e);
        }

        if ($clientRC->implementsInterface(OnlineClientInterface::class)) {
            $options = [];
            $endpoint = getenv('APIGEE_EDGE_PHP_CLIENT_ENDPOINT') ?: null;
            $username = getenv('APIGEE_EDGE_PHP_CLIENT_BASIC_AUTH_USER') ?: '';
            $password = getenv('APIGEE_EDGE_PHP_CLIENT_BASIC_AUTH_PASSWORD') ?: '';
            $httpClientFqcn = getenv('APIGEE_EDGE_PHP_CLIENT_HTTP_CLIENT');

            if ($httpClientFqcn) {
                $httpClientRC = new ReflectionClass($httpClientFqcn);
                $options[OnlineClientInterface::CONFIG_HTTP_CLIENT] = $httpClientRC->newInstance();
            }

            if (DebuggerClient::class == $clientRC->getName()) {
                $logHandler = new StreamHandler(__DIR__ . '/../../debug.log');
                // Only log the message.
                $logHandler->setFormatter(new LineFormatter('%message%', null, true));
                $logger = new Logger('debuggerClient', [$logHandler], [new PsrLogMessageProcessor()]);
                $formatter = new CurlCommandFormatter();
                $logFormat = "{request_formatted}\nStats: {time_stats}\n\n";
                $options[OnlineClientInterface::CONFIG_HTTP_CLIENT] = new DebuggerHttpClient([], $formatter, $logger, $logFormat);
            }

            /* @var \Apigee\Edge\Tests\Test\OnlineClientInterface $client */
            self::$instances[$fqcn] = $clientRC->newInstance(new BasicAuth($username, $password), $endpoint, $options);
        } elseif ($clientRC->implementsInterface(OfflineClientInterface::class)) {
            /* @var \Apigee\Edge\Tests\Test\OfflineClientInterface $client */
            self::$instances[$fqcn] = $clientRC->newInstance();
        } else {
            throw new \InvalidArgumentException(
                sprintf(
                    'Class must implements either %s interface or %s interface. Got %s.',
                    OnlineClientInterface::class,
                    OfflineClientInterface::class,
                    $clientRC->getName()
                )
            );
        }

        return self::$instances[$fqcn];
    }

    /**
     * Helper function that returns whether an API client is offline or not.
     *
     * @param \Apigee\Edge\ClientInterface $client
     *   API client.
     *
     * @return bool
     *   TRUE if the client is an offline client.
     */
    public static function isOfflineClient(ClientInterface $client): bool
    {
        return $client instanceof OfflineClientInterface;
    }
}
