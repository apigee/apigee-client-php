<?php

/*
 * Copyright 2021 Google LLC
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

namespace Apigee\Edge\Tests\Api\ApigeeX\Controller;

use Apigee\Edge\Api\ApigeeX\Controller\DeveloperAcceptedRatePlanController;
use Apigee\Edge\Api\ApigeeX\Controller\RatePlanController;
use Apigee\Edge\Api\ApigeeX\Entity\RatePlanInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\HttpClient\FileSystemResponseFactory;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

/**
 * Class DeveloperAcceptedRatePlanControllerTest.
 *
 * @group controller
 * @group apigeeX
 */
class DeveloperAcceptedRatePlanControllerTest extends AcceptedRatePlanControllerTestBase
{
    protected static $testDeveloperId = 'phpunit@example.com';

    /**
     * {@inheritdoc}
     */
    protected static function entityController(?ClientInterface $client = null): EntityControllerTesterInterface
    {
        $client = $client ?? static::defaultAPIClient();

        return new EntityControllerTester(new DeveloperAcceptedRatePlanController(static::$testDeveloperId, static::defaultTestOrganization($client), $client));
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptRatePlanResponse(): ResponseInterface
    {
        $id = static::$testDeveloperId;

        return (new FileSystemResponseFactory())->createResponseForRequest(new Request('GET', "v1/organizations/phpunit/developers/{$id}/subscriptions/phpunit"));
    }

    /**
     * {@inheritdoc}
     */
    protected function getRatePlanToAccept(): RatePlanInterface
    {
        /** @var \Apigee\Edge\Api\ApigeeX\Controller\RatePlanControllerInterface $ratePlanController */
        $ratePlanController = new RatePlanController('phpunit', static::defaultTestOrganization(static::defaultAPIClient()), static::defaultAPIClient());
        /** @var \Apigee\Edge\Api\ApigeeX\Entity\DeveloperRatePlanInterface $ratePlan */
        $ratePlan = $ratePlanController->load('developer-rev');

        return $ratePlan;
    }
}
