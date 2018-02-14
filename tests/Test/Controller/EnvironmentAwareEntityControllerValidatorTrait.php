<?php

namespace Apigee\Edge\Tests\Test\Controller;

use Apigee\Edge\HttpClient\ClientInterface;
use Apigee\Edge\Tests\Test\Mock\TestClientFactory;

/**
 * Trait EnvironmentAwareEntityControllerValidatorTrait.
 */
trait EnvironmentAwareEntityControllerValidatorTrait
{
    protected static function getEnvironment(ClientInterface $client)
    {
        if (TestClientFactory::isMockClient($client)) {
            return 'test';
        }

        return getenv('APIGEE_EDGE_PHP_SDK_ENVIRONMENT') ?: 'test';
    }
}
