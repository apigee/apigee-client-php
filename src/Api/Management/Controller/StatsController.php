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

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Query\StatsQueryInterface;
use Apigee\Edge\Api\Management\Query\StatsQueryNormalizer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\AbstractController;
use Apigee\Edge\Controller\OrganizationAwareControllerTrait;
use League\Period\Period;
use Moment\Moment;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;

/**
 * Class StatsController.
 */
class StatsController extends AbstractController implements StatsControllerInterface
{
    use OrganizationAwareControllerTrait;

    /** @var string */
    private $environment;

    /** @var \Apigee\Edge\Api\Management\Query\StatsQueryNormalizer */
    private $normalizer;

    /**
     * StatsController constructor.
     *
     * @param string $environment
     *   The environment name.
     * @param string $organization
     *   Name of the organization that the entities belongs to.
     * @param \Apigee\Edge\ClientInterface $client
     *   Apigee Edge API client.
     */
    public function __construct(string $environment, string $organization, ClientInterface $client)
    {
        parent::__construct($client);
        $this->environment = $environment;
        $this->organization = $organization;
        $this->normalizer = new StatsQueryNormalizer();
        // Return responses as an associative array instead of in Apigee Edge's mixed object-array structure to
        // make developer's life easier.
        $this->jsonDecoder = new JsonDecode(true);
    }

    /**
     * @inheritdoc
     *
     * @psalm-suppress InvalidOperand - $this->normalizer->normalize() always returns an array.
     */
    public function getMetrics(StatsQueryInterface $query, ?string $optimized = 'js'): array
    {
        $query_params = [
                '_optimized' => $optimized,
            ] + $this->normalizer->normalize($query);
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->responseToArray($this->client->get($uri));

        return $response['Response'];
    }

    /**
     * Gets API message count.
     *
     * The additional optimization on the returned data happens in the SDK. The SDK fills the gaps between returned time
     * units and analytics numbers in the returned response of Apigee Edge. If no analytics data returned for a
     * given criteria it does not fill in results with zeros, it just returns the original response.
     * (This method also asks optimized response from Apigee Edge too.)
     *
     * @param StatsQueryInterface $query
     *   Stats query object.
     *
     * @throws \Moment\MomentException
     * @throws \InvalidArgumentException
     *   Find more information in fillGapsInTimeUnitsData() method.
     *
     * @return array
     *   Response as associative array.
     *
     * @psalm-suppress PossiblyNullArgument - $query->getTimeUnit() is not null.
     */
    public function getOptimisedMetrics(StatsQueryInterface $query): array
    {
        $response = $this->getMetrics($query, 'js');
        // I no analytics data returned for a given criteria just return.
        if (empty($response['stats'])) {
            return $response;
        }
        if (null !== $query->getTimeUnit()) {
            $originalTimeUnits = $response['TimeUnit'];
            $response['TimeUnit'] = $this->fillGapsInTimeUnitsData(
                $query->getTimeRange(),
                $query->getTimeUnit(),
                $query->getTsAscending()
            );
            $this->fillGapsInMetricsData(
                $query->getTsAscending(),
                $response['TimeUnit'],
                $originalTimeUnits,
                $response['stats']['data']
            );
        }

        return $response;
    }

    /**
     * @inheritdoc
     *
     * @psalm-suppress InvalidOperand - $this->normalizer->normalize() always returns an array.
     */
    public function getMetricsByDimensions(array $dimensions, StatsQueryInterface $query, ?string $optimized = 'js'): array
    {
        $query_params = [
                '_optimized' => $optimized,
            ] + $this->normalizer->normalize($query);
        $path = $this->getBaseEndpointUri()->getPath() . implode(',', $dimensions);
        $uri = $this->getBaseEndpointUri()->withPath($path)
            ->withQuery(http_build_query($query_params));
        $response = $this->responseToArray($this->client->get($uri));

        return $response['Response'];
    }

    /**
     * Gets optimized metrics organized by dimensions.
     *
     * The additional optimization on the returned data happens in the SDK. The SDK fills the gaps between time
     * units and analytics numbers in the returned response of Apigee Edge. If no analytics data returned for a
     * given criteria it does not fill in results with zeros, it just returns the original response.
     * (This method also asks optimized response from Apigee Edge too.)
     *
     * @param array $dimensions
     *   Array of dimensions.
     * @param StatsQueryInterface $query
     *   Stats query object.
     *
     * @throws \Moment\MomentException
     * @throws \InvalidArgumentException
     *   Find more information in fillGapsInTimeUnitsData() method.
     *
     * @return array
     *   Response as associative array.
     *
     * @psalm-suppress PossiblyNullArgument - $query->getTimeUnit() is not null.
     */
    public function getOptimizedMetricsByDimensions(array $dimensions, StatsQueryInterface $query): array
    {
        $response = $this->getMetricsByDimensions($dimensions, $query, 'js');
        // I no analytics data returned for a given criteria just return.
        if (empty($response['stats'])) {
            return $response;
        }
        if (null !== $query->getTimeUnit()) {
            $originalTimeUnits = $response['TimeUnit'];
            $response['TimeUnit'] = $this->fillGapsInTimeUnitsData(
                $query->getTimeRange(),
                $query->getTimeUnit(),
                $query->getTsAscending()
            );
            foreach ($response['stats']['data'] as $key => $dimension) {
                $this->fillGapsInMetricsData(
                    $query->getTsAscending(),
                    $response['TimeUnit'],
                    $originalTimeUnits,
                    $response['stats']['data'][$key]['metric']
                );
            }
        }

        return $response;
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        // Slash in the end is always required.
        return $this->client->getUriFactory()
            ->createUri(sprintf('/organizations/%s/environments/%s/stats/', $this->organization, $this->environment));
    }

    /**
     * Fills the gaps between returned time units in the response of Apigee Edge.
     *
     * When there were no metric data for a time unit (hour, day, etc.) then those are missing from Apigee Edge response.
     * This utility function fixes this problem.
     *
     * @param Period $period
     *   Original time range from StatsQuery.
     * @param string $timeUnit
     *   Time unit from StatsQuery.
     * @param bool $tsAscending
     *
     * @throws \InvalidArgumentException
     *   If time unit is not supported by the Moment library.
     * @throws \Moment\MomentException
     *
     * @return array
     *   Array of time units in the given period.
     */
    private function fillGapsInTimeUnitsData(Period $period, string $timeUnit, bool $tsAscending)
    {
        // Moment library does not validate time units that being passed just falls back to the default behavior
        // automatically. We do want to let this library's users know that they have provided an invalid time
        // unit to this function and they should rather use the "non-optimized" methods from the controller for
        // retrieving data for these time periods.
        if (in_array($timeUnit, ['decade', 'century', 'millennium'])) {
            throw new \InvalidArgumentException(
                sprintf('The %s time unit is not supported by the https://github.com/fightbulc/moment.php library.', $timeUnit)
            );
        }
        $allTimeUnits = [];
        // Fix time unit for correct time interval calculation.
        $startDate = new Moment('@' . $period->getStartDate()->getTimestamp());
        $endDate = new Moment('@' . $period->getEndDate()->getTimestamp());
        // Returned intervals by Apigee Edge are always inclusive-inclusive.
        $startDate->startOf($timeUnit);
        $endDate->endOf($timeUnit);
        $period = new Period($startDate, $endDate);
        $timeUnit = '1 ' . $timeUnit;
        /** @var \DateTime $dateTime */
        foreach ($period->getDatePeriod($timeUnit) as $dateTime) {
            $allTimeUnits[] = $dateTime->getTimestamp() * 1000;
        }

        return $tsAscending ? $allTimeUnits : array_reverse($allTimeUnits);
    }

    /**
     * Fills the gaps between returned analytics numbers in the response of Apigee Edge.
     *
     * Apigee Edge does not returns zeros for those time units (hours, days, etc.) when there were no metric data.
     *
     * @param bool $tsAscending
     *   TsAscending from StatsQuery.
     * @param array $allTimeUnits
     *   All time units in the given time interval.
     * @param array $originalTimeUnits
     *   Returned time units by Apigee Edge.
     * @param array $metricsData
     *   Returned metrics data by Apigee Edge.
     */
    private function fillGapsInMetricsData(bool $tsAscending, array $allTimeUnits, array $originalTimeUnits, array &$metricsData): void
    {
        $zeroArray = array_fill_keys($allTimeUnits, 0);
        foreach ($metricsData as $key => $metric) {
            $metricsData[$key]['values'] = array_combine($originalTimeUnits, $metric['values']);
            $metricsData[$key]['values'] += $zeroArray;
            if ($tsAscending) {
                ksort($metricsData[$key]['values']);
            } else {
                krsort($metricsData[$key]['values']);
            }
            // Keep original numerical indexes.
            $metricsData[$key]['values'] = array_values($metricsData[$key]['values']);
        }
    }
}
