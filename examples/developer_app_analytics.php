<?php
/**
 * An example of using the Stats controller for retrieving developer app analytics.
 *
 * The original implementation of the < 2.0 version of this library can be found here: https://github.com/apigee/edge-php-sdk/blob/master/Apigee/ManagementAPI/DeveloperAppAnalytics.php
 */

use Apigee\Edge\Api\Management\Controller\DeveloperController;
use Apigee\Edge\Api\Management\Query\StatsQuery;
use Apigee\Edge\Exception\ApiException;
use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\Exception\ServerErrorException;
use League\Period\Period;

require_once 'authentication.inc';

$developerMail = getenv('APIGEE_EDGE_PHP_EXAMPLE_DEVELOPER_MAIL') ?: 'developer1@example.com';
$developerAppName = getenv('APIGEE_EDGE_PHP_EXAMPLE_DEVELOPER_APP_NAME') ?: 'test_app';

try {
    $dc = new DeveloperController($organization, $client);
    /** @var \Apigee\Edge\Api\Management\Entity\DeveloperInterface $developer */
    $developer = $dc->load($developerMail);
} catch (ClientErrorException $e) {
    // HTTP code >= 400 and < 500. Ex.: 401 Unauthorised.
    if ($e->getEdgeErrorCode()) {
        print $e->getEdgeErrorCode();
    } else {
        print $e;
    }
    throw $e;
} catch (ServerErrorException $e) {
    // HTTP code >= 500 and < 600. Ex.: 500 Server error.
    throw $e;
} catch (ApiException $e) {
    // Anything else, because this is the parent class of all the above.
    throw $e;
}

$sc = new \Apigee\Edge\Api\Management\Controller\StatsController($environment, $organization, $client);
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
        print $e->getEdgeErrorCode();
    } else {
        print $e;
    }
} catch (ServerErrorException $e) {
    // HTTP code >= 500 and < 600. Ex.: 500 Server error.
} catch (ApiException $e) {
    // Anything else, because this is the parent class of all the above.
}
