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

namespace Apigee\Edge\Tests\Api\Monetization\Controller;

use Apigee\Edge\Api\Monetization\Controller\DeveloperRoleController;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;

/**
 * Class DeveloperRoleControllerTest.
 *
 * @group controller
 * @group monetization
 */
class DeveloperRoleControllerTest extends EntityControllerTestBase
{
    public function testListing(): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Controller\DeveloperRoleControllerInterface $controller */
        $controller = static::entityController();
        $entities = $controller->getEntities();
        $input = json_decode((string) static::defaultAPIClient()->getJournal()->getLastResponse()->getBody());
        $input = reset($input);
        $i = 0;
        foreach ($entities as $role) {
            $this->entitySerializerValidator()->validate($input[$i], $role);
            ++$i;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected static function entityController(ClientInterface $client = null): EntityControllerTesterInterface
    {
        $client = $client ?? static::defaultAPIClient();

        return new EntityControllerTester(new DeveloperRoleController(static::defaultTestOrganization($client), $client));
    }
}
