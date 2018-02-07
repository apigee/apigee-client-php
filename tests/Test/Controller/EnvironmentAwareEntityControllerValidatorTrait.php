<?php

namespace Apigee\Edge\Tests\Test\Controller;

/**
 * Trait EnvironmentAwareEntityControllerValidatorTrait.
 */
trait EnvironmentAwareEntityControllerValidatorTrait
{
    protected static function getEnvironment()
    {
        return getenv('APIGEE_EDGE_PHP_SDK_ENVIRONMENT') ?: 'test';
    }
}
