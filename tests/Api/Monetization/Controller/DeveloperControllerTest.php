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

use Apigee\Edge\Api\Monetization\Controller\DeveloperController;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\DeveloperEntitySerializerValidator;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\EntitySerializerValidatorInterface;

class DeveloperControllerTest extends LegalEntityControllerTestBase
{
    /**
     * @inheritdoc
     */
    protected static function getEntityController(): EntityControllerInterface
    {
        static $controller;
        if (null === $controller) {
            $controller = new DeveloperController(static::getOrganization(static::$client), static::$client);
        }

        return $controller;
    }

    /**
     * @inheritdoc
     */
    protected function getTestEntityId(): string
    {
        return 'phpunit@example.com';
    }

    /**
     * @inheritdoc
     */
    protected static function getEntitySerializerValidator(): EntitySerializerValidatorInterface
    {
        static $validator;
        if (null === $validator) {
            $validator = new DeveloperEntitySerializerValidator(static::getEntitySerializer());
        }

        return $validator;
    }
}
