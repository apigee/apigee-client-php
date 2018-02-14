<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

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
