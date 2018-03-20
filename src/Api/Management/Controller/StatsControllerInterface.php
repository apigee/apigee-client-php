<?php

/**
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
