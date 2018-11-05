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

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Api\Management\Controller\DeveloperController;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\DefaultTestOrganizationAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;

trait DeveloperControllerAwareTestTrait
{
    use DefaultTestOrganizationAwareTrait;
    use DefaultAPIClientAwareTrait;

    /**
     * @param \Apigee\Edge\ClientInterface|null $client
     *
     * @return \Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface|\Apigee\Edge\Api\Management\Controller\DeveloperControllerInterface
     */
    protected static function developerController(ClientInterface $client = null): EntityControllerTesterInterface
    {
        $client = $client ?? static::defaultAPIClient();

        return new EntityControllerTester(new DeveloperController(static::defaultTestOrganization($client), $client));
    }
}
