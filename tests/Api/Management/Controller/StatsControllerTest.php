<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Api\Management\Controller\StatsController;
use Apigee\Edge\Api\Management\Query\StatsQuery;
use Apigee\Edge\HttpClient\Client;
use Apigee\Edge\HttpClient\Util\Builder;
use Apigee\Edge\Tests\Test\Controller\AbstractControllerValidator;
use Apigee\Edge\Tests\Test\Controller\EnvironmentAwareEntityControllerValidatorTrait;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;
use Apigee\Edge\Tests\Test\Mock\FileSystemMockClient;
use Apigee\Edge\Tests\Test\Mock\TestClientFactory;
use GuzzleHttp\Psr7\Response;
use Http\Mock\Client as MockClient;
use League\Period\Period;

/**
 * Class StatsControllerTest.
 *
 * Setting up real analytics data on Edge would take time and it can not be solved in unit tests so we have to use
 * the offline file system client for all tests.
 * Also we only test our custom methods that return "optimized" response because other methods (described by
 * StatsControllerInterface) does not do any transformation on the data, always returns the json decoded response
 * of Apigee Edge.
 *
 * @group controller
 * @group offline
 * @group mock
 */
class StatsControllerTest extends AbstractControllerValidator
{
    use EnvironmentAwareEntityControllerValidatorTrait;
    use OrganizationAwareEntityControllerValidatorTrait;

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
        // Make our life easier and use the same timezone as Edge.
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
        $httpClient = new MockClient();
        $client = new Client(null, new Builder($httpClient));
        $controller = new StatsController(static::getEnvironment($client), static::getOrganization($client), $client);
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
        $httpClient = new MockClient();
        $client = new Client(null, new Builder($httpClient));
        $httpClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode($this->emptyResponseArray())));
        $controller = new StatsController(static::getEnvironment($client), static::getOrganization($client), $client);
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

    private function emptyResponseArray(): array
    {
        return ['Response' => ['TimeUnit' => [], 'stats' => ['data' => [0 => ['values' => []]]]]];
    }

    /**
     * Returns a configured StatsController that uses the FileSystemMockClient http client.
     *
     * @return \Apigee\Edge\Api\Management\Controller\StatsController
     */
    private function getController(): StatsController
    {
        static $controller;
        if (!$controller) {
            $client = (new TestClientFactory())->getClient(FileSystemMockClient::class);
            $controller = new StatsController(static::getEnvironment($client), static::getOrganization($client), $client);
        }

        return $controller;
    }
}
