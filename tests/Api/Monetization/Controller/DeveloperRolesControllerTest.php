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
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;

class DeveloperRoleControllerTest extends OrganizationAwareEntityControllerTestBase
{
    use OrganizationAwareEntityControllerValidatorTrait;

    public function testListing(): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Controller\DeveloperRoleControllerInterface $controller */
        $controller = $this->getEntityController();
        $entities = $controller->getEntities();
        $input = json_decode((string) static::$client->getJournal()->getLastResponse()->getBody());
        $input = reset($input);
        $i = 0;
        foreach ($entities as $role) {
            $this->getEntitySerializerValidator()->validate($input[$i], $role);
            ++$i;
        }
    }

    /**
     * @inheritdoc
     */
    protected static function getEntityController(ClientInterface $client = null): EntityControllerInterface
    {
        static $controller;
        if (null === $controller) {
            $controller = new DeveloperRoleController(static::getOrganization(static::$client), static::$client);
        }

        return $controller;
    }
}
