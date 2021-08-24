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

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Api\Management\Controller\StatsController;
use Apigee\Edge\Api\Management\Query\StatsQuery;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Tests\Test\Controller\ControllerTestBase;
use Apigee\Edge\Tests\Test\Controller\FileSystemMockAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\MockClientAwareTrait;
use GuzzleHttp\Psr7\Response;
use League\Period\Period;

/**
 * Class StatsControllerTest.
 *
 * @group controller
 * @group management
 * @small
 */
class StatsControllerTest extends ControllerTestBase
{
    use FileSystemMockAPIClientAwareTrait;
    use MockClientAwareTrait;

    protected const TEST_ENVIRONMENT = 'test';

    public function testGetOptimizedMetrics(): void
    {
        $this->getOptimizedMetrics(false);
    }

    public function testGetOptimizedMetricsTsAscending(): void
    {
        $this->getOptimizedMetrics(true);
    }

    /**
     * Retrieves and validates optimized metrics data.
     *
     * @param bool $tsAscending
     *   Whether to sort results by timestamp ascending or not.
     */
    public function getOptimizedMetrics(bool $tsAscending): void
    {
        // Make our life easier and use the same timezone as Edge.
        // StatsQueryNormalizerTest ensures that date conversion works properly.
        date_default_timezone_set('UTC');
        $q = new StatsQuery(['sum(message_count)'], new Period('2018-02-01 00:00', '2018-02-28 23:59'));
        $q->setTsAscending($tsAscending);
        $q->setTimeUnit('day');
        $original = $this->getController()->getMetrics($q);
        $optimized = $this->getController()->getOptimisedMetrics($q);
        // Number of days in February.
        $this->assertCount(28, $optimized['TimeUnit']);
        foreach ($optimized['stats']['data'] as $key => $metric) {
            $original_values = array_combine($original['TimeUnit'], $original['stats']['data'][$key]['values']);
            $optimized_values = array_combine($optimized['TimeUnit'], $metric['values']);
            foreach ($optimized_values as $ts => $count) {
                if (array_key_exists($ts, $original_values)) {
                    $this->assertEquals($original_values[$ts], $count);
                } else {
                    $this->assertEquals(0, $optimized_values[$ts]);
                }
            }
        }
    }

    public function testGetOptimizedMetricsByDimensions(): void
    {
        $this->getOptimizedMetricsByDimensions(false);
    }

    public function testGetOptimizedMetricsByDimensionsTsAscending(): void
    {
        $this->getOptimizedMetricsByDimensions(true);
    }

    /**
     * Retrieves and validates optimized metrics data by dimensions.
     *
     * @param bool $tsAscending
     *   Whether to sort results by timestamp ascending or not.
     */
    public function getOptimizedMetricsByDimensions(bool $tsAscending): void
    {
        // Make our life easier and use the same timezone as Apigee Edge.
        // StatsQueryNormalizerTest ensures that date conversion works properly.
        date_default_timezone_set('UTC');
        $q = new StatsQuery(['sum(message_count), sum(is_error)'], new Period('2018-02-01 00:00', '2018-02-28 23:59'));
        $q->setTsAscending($tsAscending);
        $q->setTimeUnit('day');
        $original = $this->getController()->getMetricsByDimensions(['developer_app', 'developer'], $q);
        $optimized = $this->getController()->getOptimizedMetricsByDimensions(['developer_app', 'developer'], $q);
        // Number of days in February.
        $this->assertCount(28, $optimized['TimeUnit']);
        foreach ($optimized['stats']['data'] as $dkey => $dimension) {
            foreach ($dimension['metric'] as $key => $metric) {
                $original_values = array_combine(
                    $original['TimeUnit'],
                    $original['stats']['data'][$dkey]['metric'][$key]['values']
                );
                $optimized_values = array_combine($optimized['TimeUnit'], $metric['values']);
                foreach ($optimized_values as $ts => $count) {
                    if (array_key_exists($ts, $original_values)) {
                        $this->assertEquals($original_values[$ts], $count);
                    } else {
                        $this->assertEquals(0, $optimized_values[$ts]);
                    }
                }
            }
        }
    }

    public function testUnsupportedTimeUnit(): void
    {
        date_default_timezone_set('UTC');
        $q = new StatsQuery([], new Period('2018-02-01 00:00', '2018-02-28 23:59'));
        $client = static::mockApiClient();
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = $client->getMockHttpClient();
        $controller = new StatsController(static::TEST_ENVIRONMENT, static::defaultTestOrganization($client), $client);
        foreach (['decade', 'century', 'millennium'] as $tu) {
            $q->setTimeUnit($tu);
            $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode($this->emptyResponseArray())));
            try {
                $controller->getOptimisedMetrics($q);
            } catch (\Exception $e) {
                $this->assertInstanceOf(\InvalidArgumentException::class, $e);
            }
        }
    }

    public function testGapFilling(): void
    {
        date_default_timezone_set('UTC');
        $q = new StatsQuery([], new Period('2018-02-01 11:11', '2018-02-14 23:23'));
        $q->setTimeUnit('day');
        $client = static::mockApiClient();
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = $client->getMockHttpClient();
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode($this->emptyResponseArray())));
        $controller = new StatsController(static::TEST_ENVIRONMENT, static::defaultTestOrganization($client), $client);
        $response = $controller->getOptimisedMetrics($q);
        $this->assertCount(14, $response['TimeUnit']);
        $this->assertCount(14, $response['stats']['data'][0]['values']);
        $q->setTimeRange(new Period('2018-02-01 11:11', '2018-02-01 23:23'));
        $q->setTimeUnit('hour');
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode($this->emptyResponseArray())));
        $response = $controller->getOptimisedMetrics($q);
        // Because time interval in inclusive-inclusive.
        $this->assertCount(13, $response['TimeUnit']);
        $this->assertCount(13, $response['stats']['data'][0]['values']);
    }

    protected function emptyResponseArray(): array
    {
        return ['Response' => ['TimeUnit' => [], 'stats' => ['data' => [0 => ['name' => 'foo', 'env' => 'bar', 'values' => []]]]]];
    }

    /**
     * {@inheritdoc}
     */
    protected static function defaultAPIClient(): ClientInterface
    {
        // In this test we always use the Mock API client.
        return static::fileSystemMockClient();
    }

    /**
     * Returns a configured controller with an offline http client.
     *
     * @return \Apigee\Edge\Api\Management\Controller\StatsController
     */
    protected function getController(): StatsController
    {
        static $controller;
        if (!$controller) {
            $controller = new StatsController(static::TEST_ENVIRONMENT, static::defaultTestOrganization(static::defaultAPIClient()), static::defaultAPIClient());
        }

        return $controller;
    }
}
