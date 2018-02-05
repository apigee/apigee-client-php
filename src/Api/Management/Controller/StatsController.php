<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Query\StatsQueryInterface;
use Apigee\Edge\Api\Management\Query\StatsQueryNormalizer;
use Apigee\Edge\Controller\AbstractController;
use Apigee\Edge\Controller\OrganizationAwareControllerTrait;
use Apigee\Edge\HttpClient\ClientInterface;
use League\Period\Period;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;

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
     * @param ClientInterface|null $client
     *   Apigee Edge API client.
     */
    public function __construct(string $environment, string $organization, ClientInterface $client = null)
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
     * units and analytics numbers in the returned response of Apigee Edge.
     * (This method also asks optimized response from Apigee Edge too.)
     *
     * @param StatsQueryInterface $query
     *   Stats query object.
     *
     * @return array
     *   Response as associative array.
     *
     * @psalm-suppress PossiblyNullArgument - $query->getTimeUnit() is not null.
     */
    public function getOptimisedMetrics(StatsQueryInterface $query): array
    {
        $response = $this->getMetrics($query, 'js');
        if (null !== $query->getTimeUnit()) {
            $response['stats']['data'] = $this->fillGapsInMetricsData(
                $query->getTimeRange(),
                $query->getTimeUnit(),
                $query->getTsAscending(),
                $response['TimeUnit'],
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
     * The additional optimization on the returned data happens in the SDK. The SDK fills the gaps between returned time
     * units and analytics numbers in the returned response of Apigee Edge.
     * (This method also asks optimized response from Apigee Edge too.)
     *
     * @param array $dimensions
     *   Array of dimensions.
     * @param StatsQueryInterface $query
     *   Stats query object.
     *
     * @return array
     *   Response as associative array.
     *
     * @psalm-suppress PossiblyNullArgument - $query->getTimeUnit() is not null.
     */
    public function getOptimizedMetricsByDimensions(array $dimensions, StatsQueryInterface $query): array
    {
        $response = $this->getMetricsByDimensions($dimensions, $query, 'js');
        if (null !== $query->getTimeUnit()) {
            foreach ($response['stats']['data'] as $key => $dimension) {
                $response['stats']['data'][$key]['metric'] = $this->fillGapsInMetricsData(
                    $query->getTimeRange(),
                    $query->getTimeUnit(),
                    $query->getTsAscending(),
                    $response['TimeUnit'],
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
     * Fills the gaps between returned time units and analytics numbers in returned response of Apigee Edge.
     *
     * Apigee Edge does not returns zeros for those days there were no metric data. These days are also missing from
     * the returned time units.
     *
     * @param Period $period
     *   Original time range from StatsQuery.
     * @param string $timeUnit
     *   Time unit from StatsQuery.
     * @param bool $tsAscending
     *   TsAscending from StatsQuery.
     * @param array $responseTimeUnits
     *   Returned time units by Apigee Edge.
     * @param array $data
     *   Returned metrics data by Apigee Edge.
     *
     * @return array
     */
    private function fillGapsInMetricsData(Period $period, string $timeUnit, bool $tsAscending, array $responseTimeUnits, array $data): array
    {
        $allTimeUnits = [];
        // Fix time unit for DatePeriod calculation.
        $timeUnit = '1 ' . $timeUnit;
        /** @var \DateTime $dateTime */
        foreach ($period->getDatePeriod($timeUnit) as $dateTime) {
            $allTimeUnits[] = $dateTime->getTimestamp() * 1000;
        }
        $zeroArray = array_fill_keys($allTimeUnits, 0);
        foreach ($data as $key => $metric) {
            $data[$key]['values'] = array_combine($responseTimeUnits, $metric['values']);
            $data[$key]['values'] += $zeroArray;
            if ($tsAscending) {
                ksort($data[$key]['values']);
            } else {
                krsort($data[$key]['values']);
            }
        }

        return $data;
    }
}
