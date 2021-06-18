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

namespace Apigee\Edge\Tests\Test\Controller;

use Apigee\Edge\ClientInterface;
use Apigee\Edge\Tests\Test\OnlineClientInterface;
use Apigee\Edge\Tests\Test\TestClientFactory;
use Apigee\Edge\Tests\Test\Utility\MarkOnlineTestSkippedAwareTrait;
use Apigee\Edge\Tests\Test\Utility\RandomGenerator;
use Apigee\Edge\Tests\Test\Utility\RandomGeneratorInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

abstract class ControllerTestBase extends TestCase
{
    use DefaultTestOrganizationAwareTrait;
    use MarkOnlineTestSkippedAwareTrait;

    /**
     * {@inheritdoc}
     */
    public static function markOnlineTestSkipped(string $testName, ClientInterface $client = null): void
    {
        $client = $client ?? static::defaultAPIClient();
        if (TestClientFactory::isOfflineClient($client)) {
            Assert::markTestSkipped($testName . ' can be executed only with an online API test client.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected static function defaultTestOrganization(ClientInterface $client): string
    {
        return $client instanceof OnlineClientInterface ? getenv('APIGEE_EDGE_PHP_CLIENT_ORGANIZATION') ?? '' : 'phpunit';
    }

    /**
     * {@inheritdoc}
     */
    protected static function defaultAPIClient(): ClientInterface
    {
        return TestClientFactory::getClient();
    }

    /**
     * Returns the same random generator in tests.
     *
     * @return \Apigee\Edge\Tests\Test\Utility\RandomGeneratorInterface
     */
    protected static function randomGenerator(): RandomGeneratorInterface
    {
        static $instance;
        if (null === $instance) {
            // It is not a singleton at this moment because we wanted to be
            // sure that Faker would be able to handle that.
            return new RandomGenerator();
        }

        return $instance;
    }
}
