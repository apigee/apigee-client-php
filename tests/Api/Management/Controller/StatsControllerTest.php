<?php

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Api\Management\Controller\StatsController;
use Apigee\Edge\Api\Management\Query\StatsQuery;
use Apigee\Edge\HttpClient\Client;
use Apigee\Edge\HttpClient\Util\Builder;
use Apigee\Edge\Tests\Test\Controller\AbstractControllerValidator;
use Apigee\Edge\Tests\Test\Controller\EnvironmentAwareEntityControllerValidatorTrait;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;
use Apigee\Edge\Tests\Test\Mock\FileSystemMockClient;
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
        // Make our life easier and use the same timezone as Edge.
        // StatsQueryNormalizerTest ensures that date conversion works properly.
        date_default_timezone_set('UTC');
        $q = new StatsQuery(['sum(message_count)'], new Period('2018-02-01 00:00', '2018-02-28 23:59'));
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
        // Make our life easier and use the same timezone as Edge.
        // StatsQueryNormalizerTest ensures that date conversion works properly.
        date_default_timezone_set('UTC');
        $q = new StatsQuery(['sum(message_count), sum(is_error)'], new Period('2018-02-01 00:00', '2018-02-28 23:59'));
        $q->setTimeUnit('day');
        $original = $this->getController()->getMetricsByDimensions(['developer_app', 'developer'], $q);
        $optimized = $this->getController()->getOptimizedMetricsByDimensions(['developer_app', 'developer'], $q);
        // Number of days in February.
        $this->assertCount(28, $optimized['TimeUnit']);
        foreach ($optimized['stats']['data'] as $dkey => $dimension) {
            foreach ($dimension['metric'] as $key => $metric) {
                $original_values = array_combine($original['TimeUnit'], $original['stats']['data'][$dkey]['metric'][$key]['values']);
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

    /**
     * Returns a configured StatsController that uses the FileSystemMockClient http client.
     *
     * @return \Apigee\Edge\Api\Management\Controller\StatsController
     */
    private function getController(): StatsController
    {
        static $controller;
        if (!$controller) {
            $httpClient = new FileSystemMockClient();
            $builder = new Builder($httpClient);
            $client = new Client(null, $builder);
            $controller = new StatsController($this->getEnvironment(), $this->getOrganization(), $client);
        }

        return $controller;
    }
}
