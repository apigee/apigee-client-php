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
    public function testLoad(): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Controller\ApiPackageControllerInterface $controller */
        $controller = $this->getEntityController();
        $entity = $controller->load('phpunit');
        $input = json_decode((string) static::$client->getJournal()->getLastResponse()->getBody());
        $this->getEntitySerializerValidator()->validate($input, $entity);
    }

    protected static function getEntityController(): EntityControllerInterface
    {
        static $controller;
        if (null === $controller) {
            $controller = new ApiPackageController(static::getOrganization(static::$client), static::$client);
        }

        return $controller;
    }

    protected static function getEntitySerializerValidator(): EntitySerializerValidatorInterface
    {
        static $validator;
        if (null === $validator) {
            $validator = new ApiPackageSerializerValidator(static::getEntitySerializer());
        }

        return $validator;
    }
}
