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
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Tests\Test\Controller\EntityControllerValidator;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;
use Apigee\Edge\Tests\Test\HttpClient\FileSystemMockClient;
use Apigee\Edge\Tests\Test\TestClientFactory;

class ApiProductControllerValidator extends EntityControllerValidator
{
    use OrganizationAwareEntityControllerValidatorTrait;
    use PaginatedEntityListingControllerValidatorTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$client = (new TestClientFactory())->getClient(FileSystemMockClient::class);
    }

    public function testLoad(): void
    {
        $entity = $this->getEntityController()->load('phpunit');
        $this->assertEquals('phpunit', $entity->id());
        $this->assertEquals('This is a test product.', $entity->getDescription());
        $this->assertEquals('PHPUnit', $entity->getDisplayName());
        $this->assertEquals('phpunit', $entity->getName());
        $this->assertEquals("txProviderStatus == 'OK'", $entity->getTransactionSuccessCriteria());
        $this->assertEquals('CREATED', $entity->getStatus());
    }

    /**
     * @inheritdoc
     */
    protected static function getEntityController(ClientInterface $client = null): EntityControllerInterface
    {
        static $controller;
        if (null === $client) {
            if (null === $controller) {
                $controller = new ApiProductController(static::getOrganization(static::$client), static::$client);
            }

            return $controller;
        }

        return new ApiProductController(static::getOrganization($client), $client);
    }
}
