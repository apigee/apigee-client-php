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

namespace Apigee\Edge\Tests\Test\Controller;

use Apigee\Edge\Tests\Test\Mock\TestClientFactory;
use Faker\Factory;
use PHPUnit\Framework\TestCase;

abstract class AbstractControllerValidator extends TestCase
{
    /** @var \Apigee\Edge\HttpClient\ClientInterface */
    protected static $client;

    /** @var \Faker\Generator */
    protected static $random;

    /** @var string */
    protected static $onlyOnlineClientSkipMessage = 'Test can be executed only with real Apigee Edge connection.';

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$random = Factory::create();
        static::$client = (new TestClientFactory())->getClient();
    }
}
