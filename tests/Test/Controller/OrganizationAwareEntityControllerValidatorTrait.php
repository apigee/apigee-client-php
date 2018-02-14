<?php

namespace Apigee\Edge\Tests\Test\Controller;

use Apigee\Edge\HttpClient\ClientInterface;
use Apigee\Edge\Tests\Test\Mock\TestClientFactory;

/**
 * Trait OrganizationAwareEntityControllerValidatorTrait.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
trait OrganizationAwareEntityControllerValidatorTrait
{
    protected static function getOrganization(ClientInterface $client)
    {
        if (TestClientFactory::isMockClient($client)) {
            return 'phpunit';
        }

        return getenv('APIGEE_EDGE_PHP_SDK_ORGANIZATION') ?: 'phpunit';
    }
}
