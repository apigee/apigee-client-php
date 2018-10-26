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

use Apigee\Edge\Api\Monetization\Controller\OrganizationProfileController;
use Apigee\Edge\Api\Monetization\Controller\OrganizationProfileControllerInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\OrganizationAwareEntitySerializerValidator;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTestBase as BaseEntityControllerTesterBase;
use Apigee\Edge\Tests\Test\Controller\FileSystemMockAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerValidatorInterface;

/**
 * Base class for Monetization API tests.
 */
abstract class EntityControllerTestBase extends BaseEntityControllerTesterBase
{
    use FileSystemMockAPIClientAwareTrait;
    use OrganizationProfileControllerAwareTestTrait;

    protected static $originalTimezone;

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass(): void
    {
        // Use a specific timezone in all tests.
        static::$originalTimezone = date_default_timezone_get();
        date_default_timezone_set('Europe/Budapest');
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        date_default_timezone_set(static::$originalTimezone);
    }

    /**
     * @inheritDoc
     */
    protected static function defaultAPIClient(): ClientInterface
    {
        // Currently majority of the Monetization API tests are using the
        // file system mock API client.
        return static::fileSystemMockClient();
    }

    /**
     * @inheritDoc
     */
    protected function entitySerializerValidator(): EntitySerializerValidatorInterface
    {
        static $validator;
        if (null === $validator) {
            $validator = new OrganizationAwareEntitySerializerValidator($this->entitySerializer());
        }

        return $validator;
    }

    /**
     * @inheritDoc
     */
    protected static function organizationProfileController(): OrganizationProfileControllerInterface
    {
        return new OrganizationProfileController(static::defaultTestOrganization(static::defaultAPIClient()), static::defaultAPIClient());
    }
}
