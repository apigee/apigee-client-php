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

use Apigee\Edge\Api\Management\Controller\ApiProductController;
use Apigee\Edge\Exception\ApiRequestException;
use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\Tests\Test\TestClientFactory;

/**
 * Provides re-usable functionality for company- and developer app tests.
 *
 * @see \Apigee\Edge\Tests\Api\Management\Controller\DeveloperAppControllerBase
 * @see \Apigee\Edge\Tests\Api\Management\Controller\CompanyAppControllerBase
 */
trait CommonAppControllerTestTrait
{
    /** @var string API Product name. */
    protected static $apiProductName;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        try {
            $apc = new ApiProductController(static::getOrganization(static::$client), static::$client);
            try {
                $entity = $apc->load(ApiProductControllerTest::sampleDataForEntityCreate()->id());
                static::$apiProductName = $entity->id();
            } catch (ClientErrorException $e) {
                if ($e->getEdgeErrorCode() && 'keymanagement.service.apiproduct_doesnot_exist' === $e->getEdgeErrorCode()) {
                    $entity = clone ApiProductControllerTest::sampleDataForEntityCreate();
                    $apc->create($entity);
                    static::$apiProductName = $entity->id();
                }
            }
        } catch (ApiRequestException $e) {
            // Ensure that created test data always gets removed after an API call fails here.
            // (By default tearDownAfterClass() is not called if (any) exception occurred here.)
            static::tearDownAfterClass();
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public static function tearDownAfterClass(): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            return;
        }

        if (null !== static::$apiProductName) {
            $apc = new ApiProductController(static::getOrganization(static::$client), static::$client);
            try {
                $apc->delete(static::$apiProductName);
            } catch (\Exception $e) {
                printf("Unable to delete api product entity with %s id.\n", static::$apiProductName);
            }
        }
    }
}
