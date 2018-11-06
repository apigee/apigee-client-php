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

use Apigee\Edge\Api\Monetization\Controller\DeveloperPrepaidBalanceController;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use PHPUnit\Framework\Assert;

class DeveloperPrepaidBalanceControllerTest extends PrepaidBalanceControllerTestBase
{
    protected static $developerId = 'phpunit@example.com';

    protected static function getEntityController(): EntityControllerInterface
    {
        static $controller;
        if (null === $controller) {
            $controller = new DeveloperPrepaidBalanceController(self::$developerId, static::getOrganization(static::$client), static::$client);
        }

        return $controller;
    }

    /**
     * @inheritdoc
     */
    protected static function getMockEntityController(ClientInterface $client): EntityControllerInterface
    {
        return $controller = new DeveloperPrepaidBalanceController(self::$developerId, static::getOrganization(static::$client), $client);
    }

    protected static function validateRecurringPath(string $actual): void
    {
        $developerId = self::$developerId;
        Assert::assertEquals("/v1/mint/organizations/phpunit/developers/{$developerId}/developer-balances/recurring-setup", $actual);
    }
}
