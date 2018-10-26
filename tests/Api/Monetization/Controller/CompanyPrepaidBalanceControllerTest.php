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

use Apigee\Edge\Api\Monetization\Controller\CompanyPrepaidBalanceController;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use PHPUnit\Framework\Assert;

/**
 * Class CompanyPrepaidBalanceControllerTest.
 *
 * @group controller
 * @group monetization
 */
class CompanyPrepaidBalanceControllerTest extends PrepaidBalanceControllerTestBase
{
    protected static $companyName = 'phpunit';

    /**
     * @inheritdoc
     */
    protected static function entityController(): EntityControllerTesterInterface
    {
        return new EntityControllerTester(new CompanyPrepaidBalanceController(self::$companyName, static::defaultTestOrganization(static::defaultAPIClient()), static::defaultAPIClient()));
    }

    /**
     * @inheritdoc
     */
    protected static function getMockEntityController(): EntityControllerInterface
    {
        return $controller = new CompanyPrepaidBalanceController(self::$companyName, static::defaultTestOrganization(static::mockApiClient()), static::mockApiClient());
    }

    /**
     * @inheritdoc
     */
    protected static function validateRecurringPath(string $actual): void
    {
        $companyName = static::$companyName;
        Assert::assertEquals("/v1/mint/organizations/phpunit/companies/{$companyName}/developer-balances/recurring-setup", $actual);
    }
}
