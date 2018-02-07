<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Query\StatsQueryInterface;

/**
 * Interface StatsControllerInterface.
 *
 * @see https://docs.apigee.com/api/stats
 * @see https://docs.apigee.com/analytics-services/reference/analytics-reference
 * @see https://community.apigee.com/articles/2621/querying-analytics-apis-in-apigee-edge.html
 *
 * @TODO
 *
 * @see https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/stats/preferences/reports/dailysummaryreport
 * @see https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/stats/preferences/reports/dailysummaryreport/users
 */
interface StatsControllerInterface
{
    /**
     * Gets API message count.
     *
     * @param StatsQueryInterface $query
     *   Stats query object.
     * @param null|string $optimized
     *   Return an optimized JSON response or not. Optimization happens on Apigee Edge. Possible values: NULL or js.
     *
     * @return array
     *   Response as associative array.
     *
     * @see https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/stats
     */
    public function getMetrics(StatsQueryInterface $query, ?string $optimized = 'js'): array;

    /**
     * Gets metrics organized by dimensions.
     *
     * @param array $dimensions
     *   Array of dimensions.
     * @param StatsQueryInterface $query
     *   Stats query object.
     * @param null|string $optimized
     *   Return an optimized JSON response or not. Optimization happens on Apigee Edge. Possible values: NULL or js.
     *
     * @return array
     *   Response as associative array.
     *
     * @see https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/environments/%7Benv_name%7D/stats/%7Bdimension_name%7D-0
     */
    public function getMetricsByDimensions(array $dimensions, StatsQueryInterface $query, ?string $optimized = 'js'): array;
}
