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

use Apigee\Edge\Api\Management\Controller\DeveloperController;
use Apigee\Edge\Api\Management\Query\StatsQuery;
use Apigee\Edge\Exception\ApiException;
use Apigee\Edge\Exception\ApiRequestException;
use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\Exception\ServerErrorException;
use League\Period\Period;

require_once 'authentication.inc';

$environment = getenv('APIGEE_EDGE_PHP_SDK_ENVIRONMENT') ?: 'test';

$developerMail = getenv('APIGEE_EDGE_PHP_EXAMPLE_DEVELOPER_MAIL') ?: 'developer1@example.com';
$developerAppName = getenv('APIGEE_EDGE_PHP_EXAMPLE_DEVELOPER_APP_NAME') ?: 'test_app';

try {
    $dc = new DeveloperController($clientFactory->getOrganization(), $clientFactory->getClient());
    /** @var \Apigee\Edge\Api\Management\Entity\DeveloperInterface $developer */
    $developer = $dc->load($developerMail);
} catch (ClientErrorException $e) {
    // HTTP code >= 400 and < 500. Ex.: 401 Unauthorised.
    if ($e->getEdgeErrorCode()) {
        echo $e->getEdgeErrorCode();
    } else {
        echo $e;
    }
    throw $e;
} catch (ServerErrorException $e) {
    // HTTP code >= 500 and < 600. Ex.: 500 Server error.
    throw $e;
} catch (ApiRequestException $e) {
    // The request has failed, ex.: networking issues.
} catch (ApiException $e) {
    // Anything else, because this is the parent class of all the above.
}

$sc = new \Apigee\Edge\Api\Management\Controller\StatsController($environment, $clientFactory->getOrganization(), $clientFactory->getClient());
// Read more about Period library usage here: http://period.thephpleague.com/3.0
$q = new StatsQuery(['total_response_time'], new Period('now - 7 days', 'now'));
$q->setFilter("(developer eq '{$organization}@@@{$developer->getDeveloperId()}' and developer_app eq '{$developerAppName}')")
    ->setTimeUnit('hour')
    ->setSortBy('total_response_time')
    ->setSort(StatsQuery::SORT_ASC);
try {
    $result = $sc->getOptimizedMetricsByDimensions(['apps'], $q);
    print_r($result);
} catch (ClientErrorException $e) {
    // HTTP code >= 400 and < 500. Ex.: 401 Unauthorised.
    if ($e->getEdgeErrorCode()) {
        echo $e->getEdgeErrorCode();
    } else {
        echo $e;
    }
} catch (ServerErrorException $e) {
    // HTTP code >= 500 and < 600. Ex.: 500 Server error.
} catch (ApiRequestException $e) {
    // The request has failed, ex.: networking issues.
} catch (ApiException $e) {
    // Anything else, because this is the parent class of all the above.
}
