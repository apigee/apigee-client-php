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

use Apigee\Edge\Api\Management\Controller\DeveloperAppController;
use Apigee\Edge\Api\Management\Entity\DeveloperInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\DefaultTestOrganizationAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTester;

trait DeveloperAppControllerAwareTestTrait
{
    use DefaultTestOrganizationAwareTrait;
    use DefaultAPIClientAwareTrait;

    /**
     * @param \Apigee\Edge\ClientInterface|null $client
     *
     * @return \Apigee\Edge\Tests\Test\Controller\EntityControllerTester|\Apigee\Edge\Api\Management\Controller\DeveloperAppControllerInterface
     */
    protected static function developerAppController(ClientInterface $client = null): EntityControllerTester
    {
        $client = $client ?? static::defaultAPIClient();

        return new EntityControllerTester(new DeveloperAppController(static::defaultTestOrganization($client), static::developerAppControllerDeveloperAppOwner()->id(), $client));
    }

    abstract protected static function developerAppControllerDeveloperAppOwner(): DeveloperInterface;
}
