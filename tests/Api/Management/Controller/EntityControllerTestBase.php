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

use Apigee\Edge\Tests\Api\Management\EntitySerializer\EntitySerializerValidator;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTestBase as BaseEntityControllerTestBase;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerValidatorInterface;

/**
 * Base class for Management API tests.
 */
abstract class EntityControllerTestBase extends BaseEntityControllerTestBase
{

    protected static $validator;

    /**
     * {@inheritdoc}
     */
    public static function tearDownAfterClass(): void
    {
        // Clearing the static variables.
        static::$validator = null;
    }

    /**
     * {@inheritdoc}
     */
    protected function entitySerializerValidator(): EntitySerializerValidatorInterface
    {
        if (null === static::$validator) {
            static::$validator = new EntitySerializerValidator($this->entitySerializer());
        }

        return static::$validator;
    }
}
