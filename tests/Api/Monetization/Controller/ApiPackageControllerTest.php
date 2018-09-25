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

use Apigee\Edge\Api\Monetization\Controller\ApiPackageController;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\ApiPackageSerializerValidator;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\EntitySerializerValidatorInterface;

class ApiPackageControllerTest extends OrganizationAwareEntityControllerValidator
{
    use EntityLoadOperationControllerValidatorTrait;

    public function testGetAvailableApiPackages(): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Controller\ApiPackageControllerInterface $controller */
        $controller = $this->getEntityController();
        $packages = $controller->getAvailableApiPackagesByDeveloper('phpunit@example.com');
        $this->assertCount(2, $packages);
        $this->assertEquals('current=false&allAvailable=true', $this->getClient()->getJournal()->getLastRequest()->getUri()->getQuery());
        $json = json_decode((string) $this->getClient()->getJournal()->getLastResponse()->getBody());
        $json = reset($json);
        $i = 0;
        foreach ($packages as $package) {
            $this->getEntitySerializerValidator()->validate($json[$i], $package);
            ++$i;
        }
        $controller->getAvailableApiPackagesByDeveloper('phpunit@example.com', true, false);
        $this->assertEquals('current=true&allAvailable=false', $this->getClient()->getJournal()->getLastRequest()->getUri()->getQuery());
        $controller->getAvailableApiPackagesByCompany('phpunit', true, false);
        $this->assertEquals(200, $this->getClient()->getJournal()->getLastResponse()->getStatusCode());
    }

    public function testAddProduct(): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Controller\ApiPackageControllerInterface $controller */
        $controller = $this->getEntityController();
        // To spare some extra lines of code we use the file system http client
        // instead of the mock http client.
        $controller->addProduct('phpunit', 'product1');
        $this->assertEquals('{}', (string) $this->getClient()->getJournal()->getLastRequest()->getBody());
    }

    public function testDeleteProduct(): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Controller\ApiPackageControllerInterface $controller */
        $controller = $this->getEntityController();
        // To spare some extra lines of code we use the file system http client
        // instead of the mock http client.
        $controller->deleteProduct('phpunit', 'product1');
        $this->assertEquals(200, (string) $this->getClient()->getJournal()->getLastResponse()->getStatusCode());
    }

    /**
     * @inheritdoc
     */
    protected static function getEntityController(): EntityControllerInterface
    {
        static $controller;
        if (null === $controller) {
            $controller = new ApiPackageController(static::getOrganization(static::$client), static::$client);
        }

        return $controller;
    }

    /**
     * @inheritdoc
     */
    protected static function getEntitySerializerValidator(): EntitySerializerValidatorInterface
    {
        static $validator;
        if (null === $validator) {
            $validator = new ApiPackageSerializerValidator(static::getEntitySerializer());
        }

        return $validator;
    }
}
