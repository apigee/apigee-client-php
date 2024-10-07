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

use Apigee\Edge\Api\Monetization\Controller\ApiProductController;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;

/**
 * Class ApiProductControllerTest.
 *
 * @group controller
 * @group monetization
 */
class ApiProductControllerTest extends EntityControllerTestBase
{
    use EntityLoadOperationControllerTestTrait;

    public function testEligibleProducts(): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Controller\ApiProductControllerInterface $controller */
        $controller = static::entityController();
        $products = $controller->getEligibleProductsByDeveloper('phpunit@example.com');
        $this->assertCount(2, $products);
        $products = $controller->getEligibleProductsByCompany('phpunit');
        $this->assertCount(2, $products);
    }

    /**
     * {@inheritdoc}
     */
    protected static function entityController(?ClientInterface $client = null): EntityControllerTesterInterface
    {
        $client = $client ?? static::defaultAPIClient();

        return new EntityControllerTester(new ApiProductController(static::defaultTestOrganization($client), $client));
    }
}
